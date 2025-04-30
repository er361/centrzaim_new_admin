<?php

namespace App\Services\RecurrentScriptService\Strategies\Scenario2;

use App\Models\User;
use Psr\Log\LoggerInterface;

abstract class PaymentCondition
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Проверяет, удовлетворяет ли пользователь условию для создания платежа
     *
     * @param User $user
     * @return bool
     */
    abstract public function check(User $user): bool;

    /**
     * Создает платеж для пользователя, если условие выполнено
     *
     * @param User $user
     * @return bool
     */
    abstract public function execute(User $user): bool;

    /**
     * Проверяет условие и если оно выполнено, создает платеж
     *
     * @param User $user
     * @return bool Вернет true, если платеж создан, иначе false
     */
    public function process(User $user): bool
    {
        if ($this->check($user)) {
            return $this->execute($user);
        }

        return false;
    }
}