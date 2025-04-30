<?php


namespace App\Services\PaymentService\Impaya\Request;


use Spatie\DataTransferObject\DataTransferObject;

class Card extends DataTransferObject
{
    /**
     * @var null|string
     */
    public $pan;

    /**
     * @var null|int
     */
    public $emonth;

    /**
     * @var null|int
     */
    public $eyear;

    /**
     * @var null|string
     */
    public $cvv;

    /**
     * @var null|string
     */
    public $holder;

    /**
     * Идентификатор карты в системе Impaya или значение «Random».
     * При указании значения «Random» выбирается случайная карта из привязанных к данному пользователю
     *
     * @var null|string
     */
    public $uid;

    public function toArray(): array
    {
        $array = parent::toArray();

        return array_filter($array);
    }
}