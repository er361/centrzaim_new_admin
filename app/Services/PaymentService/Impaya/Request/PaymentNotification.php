<?php


namespace App\Services\PaymentService\Impaya\Request;


use Spatie\DataTransferObject\DataTransferObject;

class PaymentNotification extends DataTransferObject
{
    /**
     * @var string
     */
    public $MerchantContract;

    /**
     * @var string
     */
    public $OriginalOrderId;

    /**
     * @var string
     */
    public $MerchantOrderId;

    /**
     * @var int
     */
    public $Amount;

    /**
     * @var null|string
     */
    public $AuthCode = null;

    /**
     * @var null|string
     */
    public $RRN = null;

    /**
     * @var bool
     */
    public $Success;

    /**
     * @var string
     */
    public $CardNumber;

    /**
     * @var null|string
     */
    public $BankName = null;

    /**
     * @var null|string
     */
    public $ErrCode = null;

    /**
     * @var string
     */
    public $State;

    /**
     * @var string
     */
    public $Notification;

    /**
     * @var null|string
     */
    public $CardUId = null;

    /**
     * @var null|int
     */
    public $EMonth = null;

    /**
     * @var null|int
     */
    public $EYear = null;

    /**
     * @var null|string
     */
    public $CustomParams = null;

    /**
     * @var null|float
     */
    public $FeePercent = null;

    /**
     * @var null|string
     */
    public $TerminalID = null;

    /**
     * @var null|string
     */
    public $Signature = null;

    /**
     * @var bool
     */
    protected $ignoreMissing = true;
}