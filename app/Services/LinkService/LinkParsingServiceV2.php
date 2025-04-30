<?php

namespace App\Services\LinkService;

use App\Models\Conversion;
use App\Models\Sms;
use App\Models\User;
use App\Models\Webmaster;
use App\Services\LinkService\Contracts\LinkParsingServiceContract;
use App\Services\LinkService\Entities\ConversionEntity;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * Универсальный парсер ссылок для конверсий v2.
 */
class LinkParsingServiceV2 implements LinkParsingServiceContract
{
    /**
     * @var array<array-key, mixed> Конфигурация парсера (services.sources.*.conversion).
     */
    protected array $configuration;

    /**
     * @var int Идентификатор сайта
     */
    protected int $siteId;

    /**
     * @param array<array-key, mixed> $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
        $this->siteId = config('postbacks.site_id');
    }

    /**
     * Получить сущность конверсии.
     *
     * @param array $request
     * @return ConversionEntity|null
     * @throws UnknownProperties
     */
    public function getConversionEntity(array $request): ?ConversionEntity
    {
        // Leads иногда не передает
        $transactionIdField = $this->configuration['fields']['apiTransactionId'];

        if (!Arr::has($request, $transactionIdField)) {
            return null;
        }

        $siteId = $this->getSub1SiteId($request);

        // Не принадлежит текущему сайту, значит должно обработаться где-то в другом месте
        if ($siteId !== $this->siteId) {
            return null;
        }

        $sub1Models = $this->getSub1Models($request);

        $user = $sub1Models['user'] ?? null;
        $webmaster = $sub1Models['webmaster'] ?? null;
        $sms = $sub1Models['sms'] ?? null;

        $type = $this->getType($request);
        $status = $this->getStatus($request);
        $createdAt = $this->getCreatedAt($request);

        $entityConversionFields = [
            'userId' => $user->id ?? null,
            'webmasterId' => $webmaster->id ?? null,
            'smsId' => $sms->id ?? null,
            'type' => $type,
            'apiStatus' => $status,
            'apiCreatedAt' => $createdAt,
        ];

        $fieldsConfiguration = $this->configuration['fields'];
        $fieldsToFill = [
            'apiConversionId',
            'apiTransactionId',
            'apiAdvSubId',
            'apiPayout',
            'apiPayoutType',
            'apiUserAgent',
            'apiOfferId',
            'apiAffiliateId',
            'apiSource',
            'apiIp',
            'apiIsTest',
            'apiCurrency',
        ];

        foreach ($fieldsToFill as $fieldToFill) {
            $remoteFieldName = $fieldsConfiguration[$fieldToFill] ?? null;

            if ($remoteFieldName !== null) {
                $entityConversionFields[$fieldToFill] = Arr::get($request, $remoteFieldName);
            }
        }

        return new ConversionEntity($entityConversionFields);
    }

    /**
     * Получить модель из AffSub1.
     * @param array $request
     * @return array<User|Webmaster|Sms>
     */
    protected function getSub1Models(array $request): array
    {
        $subName = $this->configuration['subs'][1];
        $subModels = [];

        if (!Arr::has($request, $subName)) {
            return $subModels;
        }

        $subStr = $request[$subName];
        $subArr = explode('__', $subStr);

        foreach ($subArr as $subModel) {
            if (Str::startsWith($subModel, LinkCreatorService::PREFIX_WEBMASTER)) {
                $subModels['webmaster'] = Webmaster::query()
                    ->find(Str::replace(LinkCreatorService::PREFIX_WEBMASTER, '', $subModel));
            } elseif (Str::startsWith($subModel, LinkCreatorService::PREFIX_USER)) {
                $subModels['user'] = User::query()
                    ->find(Str::replace(LinkCreatorService::PREFIX_USER, '', $subModel));
            } elseif (Str::startsWith($subModel, LinkCreatorService::PREFIX_SMS)) {
                $subModels['sms'] = Sms::query()
                    ->find(Str::replace(LinkCreatorService::PREFIX_SMS, '', $subModel));
            }
        }

        return $subModels;
    }

    /**
     * @param array $request
     * @return null|int
     */
    protected function getSub1SiteId(array $request): ?int
    {
        $subName = $this->configuration['subs'][1];

        if (!Arr::has($request, $subName)) {
            return null;
        }

        $re = '/__' . LinkCreatorService::PREFIX_SITE_ID . '(\d+)/m';
        $subStr = $request[$subName];
        preg_match($re, $subStr, $matches);

        if (isset($matches[1])) {
            return (int)$matches[1];
        }

        return null;
    }

    /**
     * Получить тип конверсии.
     * @param array $request
     * @return string
     */
    protected function getType(array $request): string
    {
        $sub2Name = $this->configuration['subs'][2];

        if (Arr::has($request, $sub2Name) && in_array($request[$sub2Name], Conversion::TYPES)) {
            return $request[$sub2Name];
        }

        return Conversion::TYPE_DASHBOARD; // По умолчанию старые конверсии приходят без типа
    }

    /**
     * @param array $request
     * @param string $type
     * @return Sms|null
     */
    protected function getSms(array $request, string $type): ?Sms
    {
        $sub3Name = $this->configuration['subs'][3];

        if (!Arr::has($request, $sub3Name)) {
            return null;
        }

        if (!in_array($type, [Conversion::TYPE_SMS])) {
            return null;
        }

        return Sms::query()->find($request[$sub3Name]);
    }

    /**
     * Получить статус для вставки в ConversionEntity.
     * @param array $request
     * @return int|null
     */
    protected function getStatus(array $request): ?int
    {
        $statusField = $this->configuration['fields']['apiStatus'];

        if (!Arr::has($request, $statusField)) {
            return null;
        }

        $status = Arr::get($request, $statusField);

        if (in_array($status, $this->configuration['statuses']['approved'], true)) {
            return Conversion::STATUS_APPROVED;
        }

        if (in_array($status, $this->configuration['statuses']['pending'], true)) {
            return Conversion::STATUS_PENDING;
        }

        if (in_array($status, $this->configuration['statuses']['rejected'], true)) {
            return Conversion::STATUS_REJECTED;
        }

        return null;
    }

    protected function getCreatedAt(array $request): Carbon
    {
        $createdAtField = $this->configuration['fields']['apiCreatedAt'];

        // LeadCraft не передает
        if ($createdAtField === null) {
            return Carbon::now(); // Потом по этому полю строится статистика, лучше заполнять текущей датой
        }

        if (!Arr::has($request, $createdAtField)) {
            return Carbon::now();
        }

        $createdAt = Arr::get($request, $createdAtField);

        try {
            if ($this->configuration['date_format'] === null) {
                return Carbon::parse($createdAt);
            }

            return Carbon::createFromFormat($this->configuration['date_format'], $createdAt);
        } catch (InvalidFormatException $e) {
            report($e);
            return Carbon::now();
        }
    }
}
