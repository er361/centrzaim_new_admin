<?php


namespace App\Services\PaymentService\Impaya\Response;


use Spatie\DataTransferObject\DataTransferObject;

class InitResponse extends DataTransferObject
{
    /**
     * @var boolean
     */
    public $Success;

    /**
     * @var string
     */
    public $OrderId;

    /**
     * @var int
     */
    public $Amount;

    /**
     * @var string
     */
    public $ErrCode;

    /**
     * @var string
     */
    public $Type;

    /**
     * @var string
     */
    public $SessionGUID;

    /**
     * @var bool
     */
    protected $ignoreMissing = true;
}