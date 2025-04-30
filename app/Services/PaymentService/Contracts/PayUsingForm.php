<?php


namespace App\Services\PaymentService\Contracts;


use App\Models\Payment;
use App\Services\PaymentService\FormData;

interface PayUsingForm
{
    /**
     * Получить данные для генерации формы платежа.
     *
     * @param Payment $payment
     *
     * @return FormData
     */
    public function getPaymentFormData(Payment $payment): FormData;
}