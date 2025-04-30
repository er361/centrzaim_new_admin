<?php


namespace App\Services\PaymentService\Impaya\Request;


use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class InitRequest extends DataTransferObject
{
    /**
     * Тип создаваемой сессии - платежная сессия
     */
    public const TYPE_PAY = 'Pay';

    /**
     * Тип создаваемой сессии - сессия для сохранения карты
     */
    public const TYPE_ADD = 'Add';

    /**
     * Тип оплаты - одностадийная оплата.
     * В случае одностадийной операции, в результате успеха деньги будут списаны с карты пользователя.
     */
    public const PAYMENT_TYPE_ONE_STEP = 'OneStep';

    /**
     * Тип оплаты - двухстадийная оплата
     */
    public const PAYMENT_TYPE_TWO_STEP = 'TwoStep';

    /**
     * Дополнительное действие с заблокированной суммой - разблокировка.
     */
    public const ACTION_UNBLOCK = 'Unblock';

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $merchant_order_id;

    /**
     * @var int
     */
    public $amount;

    /**
     * @var null|boolean
     */
    public $add_card = null;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $payment_type;

    /**
     * Дополнительное действие с заблокированной суммой.
     * Доступно только для TwoStep.
     * @var null|string
     */
    public $action = null;

    /**
     * @var null|boolean
     */
    public $recurrent = null;

    /**
     * @var null|int
     */
    public $lifetime;

    /**
     * @var null|string
     */
    public $card_uid;

    /**
     * Не исправлять полный импорт, иначе DTO не работает.
     * @var \App\Services\PaymentService\Impaya\Request\Credentials
     */
    public $credential;

    /**
     * @var null|string
     */
    public $custom_params_raw;

    /**
     * @var \App\Services\PaymentService\Impaya\Request\Good[]
     */
    public $goods;
}