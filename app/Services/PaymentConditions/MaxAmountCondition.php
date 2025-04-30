<?php

namespace App\Services\PaymentConditions;

use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class MaxAmountCondition implements PaymentConditionInterface
{
    public function check(User $user, array $planConfiguration, CarbonImmutable $now, string $iterationUuid): bool
    {
        if ($planConfiguration['should_stop_when_charged']) {

            $paymentsSum = $user->payments()->where('status', Payment::STATUS_PAYED)->sum('amount');
            $nextAmount = $planConfiguration['recurrent'][$user->recurrent_payment_success_count % count($planConfiguration['recurrent'])]['amount'];
            $successRecurrentPayments = $user->recurrent_payment_success_count ?? 0;
            $totalRecurrentPayments = count($planConfiguration['recurrent']);
            $paymentNumber = $successRecurrentPayments % $totalRecurrentPayments;
            $paymentToProvide = Arr::get($planConfiguration['recurrent'], $paymentNumber);

            if ($paymentsSum + $paymentToProvide['amount'] > $planConfiguration['max_amount']) {
                Log::error("В результате платежа спишем денег больше, чем запланировали.",[
                    'user_id' => $user->id,
                    'max_amount' => $planConfiguration['max_amount'],
                    'payment_to_provide_amount' => $paymentToProvide['amount'],
                    'payments_sum' => $paymentsSum, // Сумма успешных платежей
                ]);
                return false;
            }
        }
        return true;
    }
}
