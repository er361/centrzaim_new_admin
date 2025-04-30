<?php


namespace App\Services\PaymentService;


use Spatie\DataTransferObject\DataTransferObject;

class PaymentData extends DataTransferObject
{
    /**
     * Не изменять на полный импорт.
     * @var \App\Models\User
     */
    public $user;

    /**
     * Не изменять на полный импорт.
     * @var \App\Models\Payment
     */
    public $payment;
}