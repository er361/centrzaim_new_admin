<?php

namespace App\Services\LinkService\Entities;

use Spatie\DataTransferObject\DataTransferObject;

class ConversionEntity extends DataTransferObject
{
    /**
     * @var int|null
     */
    public ?int $userId = null;

    /**
     * @var int|null
     */
    public ?int $webmasterId = null;

    /**
     * @var int|null
     */
    public ?int $smsId = null;

    /**
     * @var string|null Тип конверсии
     */
    public ?string $type = null;

    /**
     * @var null|int Статус конверсии (из констант модели Conversion)
     */
    public ?int $apiStatus = null;

    /**
     * @var string ID конверсии
     */
    public string $apiConversionId;

    /**
     * @var string ID транзакции
     */
    public string $apiTransactionId;

    /**
     * @var null|string SubID рекламодателя
     */
    public ?string $apiAdvSubId = null;

    /**
     * @var null|\Carbon\Carbon Дата создания конверсии
     * Не убирать полный namespace.
     */
    public ?\Carbon\Carbon $apiCreatedAt = null;

    /**
     * @var float|null Заработок
     */
    public ?float $apiPayout = null;

    /**
     * @var string|null Тип расчета выплаты
     */
    public ?string $apiPayoutType = null;

    /**
     * @var string|null User Agent
     */
    public ?string $apiUserAgent = null;

    /**
     * @var string|null ID оффера
     */
    public ?string $apiOfferId = null;

    /**
     * @var string|null ID вебмастера
     */
    public ?string $apiAffiliateId = null;

    /**
     * @var string|null Партнерский источник
     */
    public ?string $apiSource = null;

    /**
     * @var string|null IP адрес
     */
    public ?string $apiIp = null;

    /**
     * @var bool|null Тестовая конверсия
     */
    public ?bool $apiIsTest = null;

    /**
     * @var string|null Валюта оффера
     */
    public ?string $apiCurrency = null;
}