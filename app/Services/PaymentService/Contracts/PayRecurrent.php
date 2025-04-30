<?php


namespace App\Services\PaymentService\Contracts;


use App\Models\Payment;

interface PayRecurrent
{
    /**
     * Совершить рекуррентный платеж.
     *
     * @param Payment $currentPayment Текущий платеж, который необходимо провести
     * @param Payment $defaultPayment Платеж на привязку карты
     */
    public function initRecurrent(Payment $currentPayment, Payment $defaultPayment): void;
}