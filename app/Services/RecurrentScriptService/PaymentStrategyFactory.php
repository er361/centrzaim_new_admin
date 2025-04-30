<?php

namespace App\Services\RecurrentScriptService;

use App\Models\User;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\MonthlyWeeklyStrategy;
use App\Services\RecurrentScriptService\Strategies\Scenario2\Scenario2;

class PaymentStrategyFactory
{
    public static function make(User $user): PaymentStrategy
    {
        return match ($user->payment_plan) {
            0 => new MonthlyWeeklyStrategy(),
            1 => new Scenario2(),
            default => throw new \Exception("Неизвестный план: {$user->plan}")
        };
    }
}