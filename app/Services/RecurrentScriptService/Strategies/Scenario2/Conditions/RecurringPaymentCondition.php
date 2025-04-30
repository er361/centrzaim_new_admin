<?php

namespace App\Services\RecurrentScriptService\Strategies\Scenario2\Conditions;

use App\Jobs\ChargePayment;
use App\Models\Payment;
use App\Models\User;
use App\Services\RecurrentScriptService\Strategies\Scenario2\PaymentCondition;

/**
 * Проверка для регулярных платежей каждые 7 дней после успешного
 */
class RecurringPaymentCondition extends PaymentCondition
{
    /**
     * Проверяет, что прошло 7 дней с последнего успешного платежа
     *
     * @param User $user
     * @return bool
     */
    public function check(User $user): bool
    {
        $amount = config('payments.scenario2.amount');
        $recurringIntervalDays = config('payments.scenario2.recurring_interval_days');

        $lastSuccessfulPayment = $user->payments()
            ->where('status', Payment::STATUS_PAYED)
            ->where('amount', $amount)
            ->orderByDesc('created_at')
            ->first();

        if (!$lastSuccessfulPayment) {
            return false;
        }

        // Проверяем, прошло ли 7 дней с последнего успешного платежа
        $daysSinceLastSuccess = $lastSuccessfulPayment->created_at->diffInDays(now());
        $shouldCreatePayment = $daysSinceLastSuccess >= $recurringIntervalDays;

        $this->logger->debug('Проверка на регулярный платеж через 7 дней', [
            'user_id' => $user->id,
            'last_successful_payment_id' => $lastSuccessfulPayment->id,
            'last_successful_payment_date' => $lastSuccessfulPayment->created_at,
            'days_since_last_success' => $daysSinceLastSuccess,
            'recurring_interval_days' => $recurringIntervalDays,
            'should_create_payment' => $shouldCreatePayment,
        ]);

        return $shouldCreatePayment;
    }

    /**
     * Создает регулярный платеж
     *
     * @param User $user
     * @return bool
     */
    public function execute(User $user): bool
    {
        $amount = config('payments.scenario2.amount');

        $this->logger->info('Создание регулярного платежа через 7 дней', [
            'user_id' => $user->id,
            'amount' => $amount,
        ]);

        dispatch(new ChargePayment($amount, $user, Payment::SUBTYPE_MONTHLY))
            ->onQueue('payments');
        return true;
    }
}