<?php

namespace App\Services\PaymentConditions;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;

class IterationDelayCondition implements PaymentConditionInterface
{
    public function check(User $user, array $planConfiguration, CarbonImmutable $now, string $iterationUuid): bool
    {
        $successRecurrentPayments = $user->recurrent_payment_success_count ?? 0;
        $totalRecurringPayments = count($planConfiguration['recurrent']);
        $iterationNumber = $successRecurrentPayments % $totalRecurringPayments;

        if ($iterationNumber === 0) {
            $previousIterationStartedAt = $user->payments()
                ->whereTypeRecurrent()
                ->whereStatusPayed()
                ->where('iteration_number', $iterationNumber - 1)
                ->min('created_at');

            if ($previousIterationStartedAt) {
                $previousIterationStartedAt = CarbonImmutable::parse($previousIterationStartedAt);
                if ($previousIterationStartedAt->diffInDays($now) < $planConfiguration['delay_between_iteration_payments_days']) {
                    Log::error("Платеж отклонен: задержка до новой итерации не выполнена.",[
                        'user_id' => $user->id,
                        'payment_number' => 0,
                        'previous_iteration_started_at' => $previousIterationStartedAt,
                    ]);
                    return false;
                }
            }
        }
        return true;
    }
}
