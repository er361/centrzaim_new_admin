<?php

namespace App\Services\RecurrentScriptService\Strategies\MonthlyWeekly;


use App\Models\User;

abstract class PaymentHandler
{
    protected ?PaymentHandler $nextHandler = null;

    public function setNext(PaymentHandler $handler): PaymentHandler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(User $user): bool
    {
        if ($this->check($user)) {
            return true;
        }

        if ($this->nextHandler) {
            return $this->nextHandler->handle($user);
        }

        return false;
    }

    abstract protected function check(User $user): bool;
}
