<?php

namespace App\Services\RecurrentScriptService\Strategies\MonthlyWeekly;

use App\Models\User;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\Conditions\DailyRetryAfterDeclinedWeeklyCondition;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\Conditions\DailyRetryForDelayedWeeklyCondition;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\Conditions\InitialMonthlyPaymentCondition;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\Conditions\NonStandardAmountCondition;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\Conditions\RecurringMonthlyPaymentCondition;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\Conditions\RecurringWeeklyPaymentCondition;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\Conditions\WeeklyAfterDeclinedMonthlyCondition;
use Psr\Log\LoggerInterface;

class PaymentProcessor
{
    protected LoggerInterface $logger;
    protected array $conditions = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->initializeConditions();
    }

    /**
     * Инициализирует проверки в порядке их применения
     */
    protected function initializeConditions(): void
    {
        // Порядок условий имеет значение!
        $this->conditions = [
            new InitialMonthlyPaymentCondition($this->logger),
            new WeeklyAfterDeclinedMonthlyCondition($this->logger),
            new RecurringMonthlyPaymentCondition($this->logger),
            new RecurringWeeklyPaymentCondition($this->logger),
            new DailyRetryAfterDeclinedWeeklyCondition($this->logger),
            new DailyRetryForDelayedWeeklyCondition($this->logger),
            new NonStandardAmountCondition($this->logger),
        ];
    }

    /**
     * Обрабатывает пользователя, последовательно применяя все проверки
     * и выполняя первое подходящее условие
     *
     * @param User $user
     * @return bool
     */
    public function processUser(User $user): bool
    {
        $this->logger->info('Начинаем обработку пользователя', [
            'user_id' => $user->id,
        ]);

        foreach ($this->conditions as $condition) {
            $conditionClass = get_class($condition);
            $this->logger->debug("Применяем условие {$conditionClass}", [
                'user_id' => $user->id,
            ]);
            
            if ($condition->process($user)) {
                $this->logger->info("Условие {$conditionClass} выполнено, платеж создан", [
                    'user_id' => $user->id,
                ]);
                return true;
            }
        }

        $this->logger->info('Ни одно условие не выполнено, платеж не создан', [
            'user_id' => $user->id,
        ]);
        return false;
    }
}