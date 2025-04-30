<?php


namespace App\Services\PaymentService;


use Spatie\DataTransferObject\DataTransferObject;

class FormData extends DataTransferObject
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $fields;
}