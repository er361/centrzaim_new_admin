<?php

namespace App\Models;

use App\Builders\SmsUserBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $sms_id
 * @property int $user_id ID пользователя
 * @property null|string $api_id Внешний идентификатор SMS
 * @property float $cost Стоимость отправки SMS
 * @property int $service_id ID провайдера, используемого для отправки
 * @property null|string $error Текст ошибки при отправке SMS
 * @property int $status Статус отправки SMS
 *
 * @method static SmsUserBuilder query()
 */
class SmsUser extends Pivot
{
    public const STATUS_SEND = 0;
    public const STATUS_DELIVERED = 1;
    public const STATUS_FAILED = 2;
    public const STATUS_NOT_KNOWN = 4;

    /**
     * При отправке SMS получили ошибку, что данному пользователю не может быть отправлено SMS сообщение.
     */
    public const STATUS_SENDING_FAILED = 5;

    /**
     * Сервис SMS не возвращает информацию о статусе SMS.
     */
    public const STATUS_EXPIRED = 6;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'sms_id',
        'user_id',
        'api_id',
        'status',
        'cost',
        'service_id',
        'error',
    ];

    /**
     * @param $query
     * @return SmsUserBuilder
     */
    public function newEloquentBuilder($query): SmsUserBuilder
    {
        return new SmsUserBuilder($query);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function sms(): BelongsTo
    {
        return $this->belongsTo(Sms::class);
    }
}