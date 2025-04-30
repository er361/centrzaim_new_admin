<?php

namespace App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\Conditions;

use App\Models\Payment;
use App\Models\User;
use App\Services\RecurrentScriptService\PaymentService;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\PaymentCondition;

/**
 * Проверка для создания недельного платежа после отклонения месячного
 */
class WeeklyAfterDeclinedMonthlyCondition extends PaymentCondition
{
    /**
     * Проверяет, что последний платеж был месячным и отклонен
     *
     * @param User $user
     * @return bool
     */
    public function check(User $user): bool
    {
        $monthlyAmount = config('payments.monthly_weekly.monthly.amount');

        $lastDeclinedMonthlyPayment = $user->payments()
            ->where('status', Payment::STATUS_DECLINED)
            ->where('amount', $monthlyAmount)
            ->orderByDesc('created_at')
            ->first();
            
        if (!$lastDeclinedMonthlyPayment) {
            return false;
        }
        
        $weeklyAmount = config('payments.monthly_weekly.weekly.amount');
        
        $hasWeeklyAttempt = $user->payments()
            ->where('amount', $weeklyAmount)
            ->exists();
        
        $this->logger->debug('Проверка на недельный платеж после отклонения месячного', [
            'user_id' => $user->id,
            'has_weekly_attempt' => $hasWeeklyAttempt,
            'last_declined_monthly_payment_id' => $lastDeclinedMonthlyPayment->id,
        ]);
        
        return !$hasWeeklyAttempt;
    }

    /**
     * Создает недельный платеж после отклонения месячного
     *
     * @param User $user
     * @return bool
     */
    public function execute(User $user): bool
    {
        $amount = config('payments.monthly_weekly.weekly.amount');

        $this->logger->info('Создание недельного платежа после отклонения месячного', [
            'user_id' => $user->id,
            'amount' => $amount,
        ]);

        PaymentService::createPayment($amount, $user, Payment::SUBTYPE_WEEKLY);
        return true;
    }
}