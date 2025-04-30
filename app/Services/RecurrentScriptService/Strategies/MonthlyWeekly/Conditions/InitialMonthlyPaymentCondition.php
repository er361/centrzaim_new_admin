<?php

namespace App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\Conditions;

use App\Models\Payment;
use App\Models\User;
use App\Services\RecurrentScriptService\PaymentService;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\PaymentCondition;

/**
 * Проверка для создания первого месячного платежа
 */
class InitialMonthlyPaymentCondition extends PaymentCondition
{
    /**
     * Проверяет, что у пользователя нет платежей
     *
     * @param User $user
     * @return bool
     */
    public function check(User $user): bool
    {
        $hasPayments = $user->payments()->exists();

        $this->logger->debug('Проверка на первый платеж', [
            'user_id' => $user->id,
            'has_payments' => $hasPayments,
        ]);

        return !$hasPayments;
    }

    /**
     * Создает первый месячный платеж
     *
     * @param User $user
     * @return bool
     */
    public function execute(User $user): bool
    {
        $amount = config('payments.monthly_weekly.monthly.amount');

        $this->logger->info('Создание первого месячного платежа', [
            'user_id' => $user->id,
            'amount' => $amount,
        ]);

        PaymentService::createPayment($amount, $user, Payment::SUBTYPE_MONTHLY);
        return true;
    }
}