<?php


namespace App\Services\PaymentService\Impaya\Response;


use Spatie\DataTransferObject\DataTransferObject;

class Card extends DataTransferObject
{
    /**
     * @var string
     */
    public $PanMask;

    /**
     * @var string
     */
    public $CardUId;

    /**
     * @var int
     */
    public $EMonth;

    /**
     * @var int
     */
    public $EYear;

    /**
     * @var string
     */
    public $Status;

    /**
     * @var string
     */
    public $CardHolder;

    /**
     * @var bool
     */
    protected $ignoreMissing = true;
}