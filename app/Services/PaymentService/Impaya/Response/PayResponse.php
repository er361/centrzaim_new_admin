<?php


namespace App\Services\PaymentService\Impaya\Response;


use Spatie\DataTransferObject\DataTransferObject;

class PayResponse extends DataTransferObject
{
    /**
     * @var boolean
     */
    public $Success;

    /**
     * @var null|string
     */
    public $OrderId = null;

    /**
     * @var null|int
     */
    public $Amount = null;

    /**
     * @var null|string
     */
    public $ErrCode = null;

    /**
     * @var null|string
     */
    public $ErrMessage = null;

    /**
     * @var bool
     */
    protected $ignoreMissing = true;
}