<?php


namespace App\Services\PaymentService\Impaya\Request;


use Spatie\DataTransferObject\DataTransferObject;

class Credentials extends DataTransferObject
{
    /**
     * @var null|string
     */
    public $login = null;

    /**
     * @var null|string
     */
    public $password = null;

    /**
     * @var null|string
     */
    public $merchant_name;

    /**
     * @var null|string
     */
    public $merchant_password;

    /**
     * @var null|string
     */
    public $terminal_password = null;

    public function toArray(): array
    {
        $array = parent::toArray();

        return array_filter($array);
    }
}