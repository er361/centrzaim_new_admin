<?php


namespace App\Services\PaymentService\Contracts;


interface PaymentServiceInterface
{
    /**
     * Получить сервис платежа для сохранения в Payments.
     * @return int
     */
    public function getService(): int;
}