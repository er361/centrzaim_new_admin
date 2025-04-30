<?php

namespace App\Services\PaymentConditions;

use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class AmountLimitCondition implements PaymentConditionInterface
{

    public function check(User $user, array $planConfiguration, CarbonImmutable $now, string $iterationUuid): bool
    {
        $successRecurrentPayments = $user->recurrent_payment_success_count;
        $totalRecurrentPayments = count($planConfiguration['recurrent']);
        $paymentNumber = $successRecurrentPayments % $totalRecurrentPayments;
        $iterationNumber = (int) floor($successRecurrentPayments / $totalRecurrentPayments);
        $paymentToProvide = Arr::get($planConfiguration['recurrent'], $paymentNumber);
        $amount = $paymentToProvide['amount'];

        $shouldStopWhenCharged = $planConfiguration['should_stop_when_charged'];
        $paymentsSum = $user->payments()
            ->where('status', Payment::STATUS_PAYED)
            ->sum('amount');

        if ($shouldStopWhenCharged && ($paymentsSum + $amount > $planConfiguration['max_amount'])) {
            Log::error('В результате платежа спишем денег больше, чем запланировали.', [
                'user_id' => $user->id,
                'max_amount' => $planConfiguration['max_amount'],
                'payment_to_provide_amount' => $amount,
                'payments_sum' => $paymentsSum,
            ]);
            $this->warn('В результате платежа спишем денег больше, чем запланировали.');
            return true;
        }

        return false;
    }
}