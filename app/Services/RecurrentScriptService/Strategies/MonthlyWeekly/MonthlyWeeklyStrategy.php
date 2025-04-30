<?php

namespace App\Services\RecurrentScriptService\Strategies\MonthlyWeekly;

use App;
use App\Builders\UserBuilder;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\RecurrentScriptService\PaymentService;
use App\Services\RecurrentScriptService\PaymentStrategy;
use Carbon\Carbon;

class MonthlyWeeklyStrategy extends PaymentStrategy
{
    /**
     * Обрабатывает платежи для конкретного пользователя
     *
     * @param User $user
     * @return bool
     */
    protected function processCharge(User $user)
    {
        // Определяем, находимся ли мы в тестовом режиме
        // В тестах важно, чтобы логика работала в точности как старый код
        $isTestMode = App::environment('testing');


        // Для реального приложения используем новую структуру
        $processor = new PaymentProcessor($this->logger);


        return $processor->processUser($user);
    }

    /**
     * Возвращает название стратегии
     *
     * @return string
     */
    protected function defineName(): string
    {
        return 'monthly_weekly';
    }
}