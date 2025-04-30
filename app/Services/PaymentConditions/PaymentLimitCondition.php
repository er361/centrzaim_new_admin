<?php

namespace App\Services\PaymentConditions;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;

class PaymentLimitCondition implements PaymentConditionInterface
{
    protected int $paymentsCanBeCreated;
    private int $paymentsLastHour;
    private int $paymentLimit;

    public function __construct(int $paymentsCanBeCreated, int $paymentsLastHour)
    {
        $paymentLimit = config('payments.recurrent_payments_per_hour') ?? 0;
        $this->paymentsCanBeCreated = $paymentsCanBeCreated;
        $this->paymentsLastHour = $paymentsLastHour;
        $this->paymentLimit = $paymentLimit;
    }

    public function check(User $user, array $planConfiguration, CarbonImmutable $now, string $iterationUuid): bool
    {

        if ($this->paymentsCanBeCreated <= 0) {
            Log::warning('Превышен лимит платежей в час, завершаем все списания.', [
                'process' => $iterationUuid,
                'payment_limit' => $this->paymentLimit,
                'payments_last_hour' => $this->paymentsLastHour,
                'payments_can_be_created' => $this->paymentsCanBeCreated,
            ]);
            return false;
        }
        return true;
    }

    public function decrement(): void
    {
        $this->paymentsCanBeCreated--;
    }
}
