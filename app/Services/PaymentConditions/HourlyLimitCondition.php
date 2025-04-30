<?php

namespace App\Services\PaymentConditions;

use App\Models\Payment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;

class HourlyLimitCondition implements PaymentConditionInterface
{
    public function check(User $user, array $planConfiguration, CarbonImmutable $now, string $iterationUuid): bool
    {
        $paymentLimit = config('payments.recurrent_payments_per_hour');
        $paymentsLastHour = Payment::query()
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->whereTypeRecurrent()
            ->count();

        $paymentsCanBeCreated = $paymentLimit - $paymentsLastHour;

        if ($paymentsCanBeCreated <= 0) {
            Log::debug('Превышен лимит платежей в час, не начинаем списание.',[
                'payment_limit' => $paymentLimit,
                'payments_last_hour' => $paymentsLastHour,
                'payments_can_be_created' => $paymentsCanBeCreated,
            ]);
            return false;
        }

        return true;
    }
}
