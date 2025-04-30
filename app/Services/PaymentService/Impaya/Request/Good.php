<?php


namespace App\Services\PaymentService\Impaya\Request;


use Spatie\DataTransferObject\DataTransferObject;

class Good extends DataTransferObject
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $price;

    /**
     * @var null|int
     */
    public $tax;
}