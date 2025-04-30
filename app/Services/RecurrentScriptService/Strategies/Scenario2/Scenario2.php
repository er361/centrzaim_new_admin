<?php

namespace App\Services\RecurrentScriptService\Strategies\Scenario2;

use App\Models\User;
use App\Services\RecurrentScriptService\PaymentStrategy;

class Scenario2 extends PaymentStrategy
{
    /**
     * Обрабатывает платежи для конкретного пользователя по сценарию 2
     *
     * @param User $user
     * @return bool
     */
    protected function processCharge(User $user)
    {
        $processor = new Scenario2Processor($this->logger);
        return $processor->processUser($user);
    }

    /**
     * Возвращает название стратегии
     *
     * @return string
     */
    protected function defineName(): string
    {
        return 'scenario_2';
    }
}