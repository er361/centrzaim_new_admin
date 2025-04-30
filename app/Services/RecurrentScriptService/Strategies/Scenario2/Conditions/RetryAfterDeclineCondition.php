<?php

namespace App\Services\RecurrentScriptService\Strategies\Scenario2\Conditions;

use App\Jobs\ChargePayment;
use App\Models\Payment;
use App\Models\User;
use App\Services\RecurrentScriptService\Strategies\Scenario2\PaymentCondition;

/**
 * Проверка для повторной попытки через 120 минут после неудачного платежа
 */
class RetryAfterDeclineCondition extends PaymentCondition
{
    /**
     * Проверяет, что последний платеж был отклонен и прошло 120 минут
     *
     * @param User $user
     * @return bool
     */
    public function check(User $user): bool
    {
        $amount = config('payments.scenario2.amount');
        $retryIntervalMinutes = config('payments.scenario2.retry_interval_minutes');

        $lastDeclinedPayment = $user->payments()
            ->where('status', Payment::STATUS_DECLINED)
            ->where('amount', $amount)
            ->orderByDesc('created_at')
            ->first();

        if (!$lastDeclinedPayment) {
            return false;
        }

        // Проверяем, прошло ли 120 минут с последней неудачной попытки
        $minutesSinceDecline = $lastDeclinedPayment->created_at->diffInMinutes(now());
        $shouldCreatePayment = $minutesSinceDecline >= $retryIntervalMinutes;

        $this->logger->debug('Проверка на повторную попытку через 120 минут', [
            'user_id' => $user->id,
            'last_declined_payment_id' => $lastDeclinedPayment->id,
            'last_declined_payment_date' => $lastDeclinedPayment->created_at,
            'minutes_since_decline' => $minutesSinceDecline,
            'retry_interval_minutes' => $retryIntervalMinutes,
            'should_create_payment' => $shouldCreatePayment,
        ]);

        return $shouldCreatePayment;
    }

    /**
     * Создает повторный платеж
     *
     * @param User $user
     * @return bool
     */
    public function execute(User $user): bool
    {
        $amount = config('payments.scenario2.amount');

        $this->logger->info('Создание повторного платежа через 120 минут после неудачной попытки', [
            'user_id' => $user->id,
            'amount' => $amount,
        ]);

        dispatch(new ChargePayment($amount, $user, Payment::SUBTYPE_MONTHLY))
            ->onQueue('payments');
        return true;
    }
}