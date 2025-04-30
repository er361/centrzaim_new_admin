<?php

namespace App\Services\RecurrentScriptService;


use App\Jobs\ChargePayment;
use App\Models\User;

class PaymentService
{
    public function processCharge(User $user)
    {
        $strategy = PaymentStrategyFactory::make($user);
        return $strategy->charge($user);
    }

    /**
     * @param $amount
     * @param User $user
     * @param int $subtype
     * @return void
     */
    public static function createPayment($amount, User $user, int $subtype): void
    {
        dispatch(new ChargePayment($amount, $user, $subtype))
            ->onQueue('payments');
    }
}