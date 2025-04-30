<?php

namespace App\Services\RecurrentScriptService\Strategies\Scenario2;

use App\Models\User;
use App\Services\RecurrentScriptService\Strategies\Scenario2\Conditions\InitialPaymentCondition;
use App\Services\RecurrentScriptService\Strategies\Scenario2\Conditions\RetryAfterDeclineCondition;
use App\Services\RecurrentScriptService\Strategies\Scenario2\Conditions\RecurringPaymentCondition;
use Psr\Log\LoggerInterface;

class Scenario2Processor
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
            new InitialPaymentCondition($this->logger),
            new RetryAfterDeclineCondition($this->logger),
            new RecurringPaymentCondition($this->logger),
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
        $this->logger->info('Начинаем обработку пользователя по сценарию 2', [
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