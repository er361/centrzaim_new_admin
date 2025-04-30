<?php

namespace App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\Conditions;

use App\Models\Payment;
use App\Models\User;
use App\Services\RecurrentScriptService\PaymentService;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\PaymentCondition;

/**
 * Проверка для платежей с нестандартными суммами
 */
class NonStandardAmountCondition extends PaymentCondition
{
    /**
     * Проверяет, что последний платеж имеет нестандартную сумму
     *
     * @param User $user
     * @return bool
     */
    public function check(User $user): bool
    {
        $monthlyAmount = config('payments.monthly_weekly.monthly.amount');
        $weeklyAmount = config('payments.monthly_weekly.weekly.amount');

        $customPayment = $user->payments()
            ->where('amount', '!=', $monthlyAmount)
            ->where('amount', '!=', $weeklyAmount)
            ->orderByDesc('created_at')
            ->first();
            
        if (!$customPayment) {
            return false;
        }
        
        $this->logger->debug('Обнаружен платеж с нестандартной суммой', [
            'user_id' => $user->id,
            'payment_id' => $customPayment->id,
            'amount' => $customPayment->amount,
        ]);
        
        return true;
    }

    /**
     * Создает недельный платеж
     *
     * @param User $user
     * @return bool
     */
    public function execute(User $user): bool
    {
        $amount = config('payments.monthly_weekly.weekly.amount');

        $this->logger->info('Создание недельного платежа после обнаружения нестандартной суммы', [
            'user_id' => $user->id,
            'amount' => $amount,
        ]);

        PaymentService::createPayment($amount, $user, Payment::SUBTYPE_WEEKLY);
        return true;
    }
}