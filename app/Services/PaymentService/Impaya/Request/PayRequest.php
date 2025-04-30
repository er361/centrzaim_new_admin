<?php


namespace App\Services\PaymentService\Impaya\Request;


use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class PayRequest extends DataTransferObject
{
    /**
     * @var string
     */
    public $key;

    /**
     * Не исправлять полный импорт, иначе DTO не работает.
     * @var \App\Services\PaymentService\Impaya\Request\Card
     */
    public $card;

    /**
     * Не исправлять полный импорт, иначе DTO не работает.
     * @var \App\Services\PaymentService\Impaya\Request\Credentials
     */
    public $credential;

    /**
     * @var string
     */
    public $merchant_order_id;

    /**
     * @var int
     */
    public $amount;

    /**
     * @var \App\Services\PaymentService\Impaya\Request\Good[]
     */
    public $goods;

    /**
     * @var string
     */
    public $custom_params;
}