<?php

namespace App\Services\PaymentConditions;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PaymentTimingCondition implements PaymentConditionInterface
{

    public function check(User $user, array $planConfiguration, CarbonImmutable $now, string $iterationUuid): bool
    {
        $successRecurrentPayments = $user->recurrent_payment_success_count;
        $totalRecurrentPayments = count($planConfiguration['recurrent']);
        $paymentNumber = $successRecurrentPayments % $totalRecurrentPayments;
        $iterationNumber = (int) floor($successRecurrentPayments / $totalRecurrentPayments);

        $paymentToProvide = Arr::get($planConfiguration['recurrent'], $paymentNumber);

        // Если это первая итерация или нет ограничений на количество списаний
        if ($planConfiguration['should_stop_when_charged'] || $iterationNumber === 0) {
            if ($user->created_at->diffInMinutes($now) < $paymentToProvide['after_minutes']) {
                Log::warning('Платеж отклонен: недостаточно времени с момента регистрации пользователя для списания.', [
                    'user_id' => $user->id,
                    'created_at' => $user->created_at,
                    'after_minutes' => $paymentToProvide['after_minutes'],
                ]);
                return false;
            }
        } else {
            // Проверка времени для промежуточного платежа в текущей итерации
            $samePaymentInPreviousIterationCreatedAt = $user->payments()
                ->whereTypeRecurrent()
                ->whereStatusPayed()
                ->wherePaymentNumber($paymentNumber)
                ->whereIterationNumber($iterationNumber - 1)
                ->min('created_at');

            if (Carbon::parse($samePaymentInPreviousIterationCreatedAt)->diffInDays($now) < $planConfiguration['delay_between_iteration_payments_days']) {
                Log::warning('Платеж отклонен: задержка до повторного платежа внутри итерации не выполнена.');
                return false;
            }
        }

        return true;
    }
}