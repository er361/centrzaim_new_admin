<?php

namespace App\Services\PaymentConditions;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PaymentLimitCheckCondition implements PaymentConditionInterface
{

    public function check(User $user, array $planConfiguration, CarbonImmutable $now, string $iterationUuid): bool
    {
        $successRecurrentPayments = $user->recurrent_payment_success_count ?? 0;
        $shouldStopWhenCharged = $planConfiguration['should_stop_when_charged'];

        if ($shouldStopWhenCharged && !Arr::has($planConfiguration['recurrent'], [$successRecurrentPayments])) {
            Log::error('Уже списали у пользователя все необходимые платежи.', [
                'user_id' => $user->id,
                'success_recurrent_payments' => $successRecurrentPayments,
            ]);
            return false;
        }

        return true;
    }
}