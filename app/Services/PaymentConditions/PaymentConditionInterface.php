<?php

namespace App\Services\PaymentConditions;


use App\Models\User;
use Carbon\CarbonImmutable;

interface PaymentConditionInterface
{
    /**
     * Выполняет проверку на возможность списания.
     *
     * @param User $user Пользователь, для которого происходит списание.
     * @param array $planConfiguration Конфигурация плана платежа.
     * @return bool Возвращает true, если проверка пройдена успешно, иначе false.
     */
    public function check(User $user, array $planConfiguration, CarbonImmutable $now, string $iterationUuid): bool;
}