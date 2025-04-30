<?php

namespace App\Models;

use App\Builders\ConversionBuilder;
use App\Builders\UserBuilder;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property null|int $user_id
 * @property string $api_conversion_id
 * @property string $api_transaction_id
 * @property null|float $api_payout
 * @property null|int $api_status
 * @property null|CarbonInterface $api_created_at
 * @property null|string $api_offer_id Идентификатор оффера в партнерской программе
 * @property null|int $source_id Идентификатор партнерской программы, откуда пришла конверсия
 *
 * @todo Описать все поля (в БД их больше)
 *
 * Relations:
 * @property-read null|User $user
 * @property-read null|Source $source
 *
 * Виртуальные свйоства:
 * @property-read string $status_text
 *
 * @method static ConversionBuilder query()
 */
class Conversion extends Model
{
    /**
     * Тип конверсии - из панели управления.
     */
    public const TYPE_DASHBOARD = 'dashboard';

    /**
     * Публичная панель займов.
     */
    public const TYPE_PUBLIC_DASHBOARD = 'public_dashboard';

    /**
     * Тип конверсии - из SMS.
     */
    public const TYPE_SMS = 'sms';

    /**
     * Возможные типы конверсий.
     */
    public const TYPES = [
        self::TYPE_DASHBOARD,
        self::TYPE_PUBLIC_DASHBOARD,
        self::TYPE_SMS,
    ];

    public const STATUS_APPROVED = 1;
    public const STATUS_REJECTED = 2;
    public const STATUS_PENDING = 3;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'user_id',
        'webmaster_id',
        'sms_id',
        'api_conversion_id',
        'api_transaction_id',
        'api_adv_sub_id',
        'api_created_at',
        'api_status',
        'api_payout',
        'api_payout_type',
        'api_user_agent',
        'api_offer_id',
        'api_affiliate_id',
        'api_source',
        'api_ip',
        'api_is_test',
        'api_currency',
        'source_id',
    ];

    protected $casts = [
        'api_created_at' => 'datetime',
        'api_status' => 'int', // todo Поменять тип колонки в базе, перевести на Enum
    ];

    /**
     * @param $query
     * @return ConversionBuilder
     */
    public function newEloquentBuilder($query): ConversionBuilder
    {
        return new ConversionBuilder($query);
    }

    /**
     * @return BelongsTo|UserBuilder
     */
    public function user(): BelongsTo|UserBuilder
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function webmaster(): BelongsTo
    {
        return $this->belongsTo(Webmaster::class);
    }

    /**
     * @return BelongsTo
     */
    public function source(): BelongsTo {
        return $this->belongsTo(Source::class);
    }

    /**
     * @return string
     */
    protected function getStatusTextAttribute(): string
    {
        return match ($this->api_status) {
            self::STATUS_APPROVED => 'Одобрена',
            self::STATUS_REJECTED => 'Отклонена',
            self::STATUS_PENDING => 'В ожидании',
            null => 'Нет статуса',
            default => throw new \InvalidArgumentException('Неизвестный статус конверсии'),
        };
    }
}
