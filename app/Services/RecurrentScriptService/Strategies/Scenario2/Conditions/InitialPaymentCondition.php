<?php

namespace App\Services\RecurrentScriptService\Strategies\Scenario2\Conditions;

use App\Jobs\ChargePayment;
use App\Models\Payment;
use App\Models\User;
use App\Services\RecurrentScriptService\Strategies\Scenario2\PaymentCondition;

/**
 * Проверка для создания первого платежа через 10 минут после регистрации
 */
class InitialPaymentCondition extends PaymentCondition
{
    /**
     * Проверяет, что прошло 10 минут после регистрации и нет платежей
     *
     * @param User $user
     * @return bool
     */
    public function check(User $user): bool
    {
        $amount = config('payments.scenario2.amount');
        $initialDelayMinutes = config('payments.scenario2.initial_delay_minutes');

        // Проверяем, что у пользователя нет платежей сценария 2
        $hasScenario2Payments = $user->payments()
            ->where('amount', $amount)
            ->exists();

        if ($hasScenario2Payments) {
            return false;
        }

        // Проверяем, прошло ли 10 минут с момента регистрации
        $minutesSinceCreation = $user->created_at->diffInMinutes(now());
        $shouldCreatePayment = $minutesSinceCreation >= $initialDelayMinutes;

        $this->logger->debug('Проверка на первый платеж через 10 минут', [
            'user_id' => $user->id,
            'created_at' => $user->created_at,
            'minutes_since_creation' => $minutesSinceCreation,
            'initial_delay_minutes' => $initialDelayMinutes,
            'should_create_payment' => $shouldCreatePayment,
        ]);

        return $shouldCreatePayment;
    }

    /**
     * Создает первый платеж
     *
     * @param User $user
     * @return bool
     */
    public function execute(User $user): bool
    {
        $amount = config('payments.scenario2.amount');

        $this->logger->info('Создание первого платежа через 10 минут после регистрации', [
            'user_id' => $user->id,
            'amount' => $amount,
        ]);

        dispatch(new ChargePayment($amount, $user, Payment::SUBTYPE_MONTHLY))
            ->onQueue('payments');
        return true;
    }
}