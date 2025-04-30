<?php

namespace App\Models;

use App\Builders\SmsBuilder;
use App\Enums\SmsTypeEnum;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Class Sms.
 *
 * @property-read int $id Идентификатор сообщения
 * @property SmsTypeEnum $type Тип сообщения
 * @property string $name Название сообщения
 * @property string $text Текст сообщения
 * @property int $delay Задержка в минутах перед отправкой (с момента регистрации пользователя)
 * @property null|string $link Ссылка для замены в тексте сообщения
 * @property string $is_enabled Время, после которого пользователь должен зарегистрироваться, чтобы получить сообщение
 * @property CarbonInterface $registered_after Отправлять только зарегистрированным после
 * @property int $sms_provider_id ID провайдера, с помощью которого отправляем сообщение
 * @property null|int $source_id Партнерская программа, для пользователей которой показываем ссылку
 * @property int $link_source_id Партнерская программа, на которую ведет ссылка
 * @property int $showcase_id Идентификатор внешней витрины, на которую надо вставить ссылку (связана с колонкой type в LoanOffer)
 * @property int $related_sms_id Идентификатор связанной SMS (для SMS AfterClick - после клика в какой SMS текущую нужно отправлять)
 *
 * Relations:
 * @property-read SmsProvider $smsProvider Провайдер, с помощью которого отправляем сообщение
 * @property-read Carbon $created_at Дата создания SMS
 * @property-read null|Source $source Партнерская программа, для пользователей которой показываем ссылку
 * @property-read null|Source $linkSource Партнерская программа, на которую ведет ссылка
 * @property-read Collection<int, Webmaster> $excludedWebmasters Вебмастера, которые не должны получать SMS
 * @property-read Collection<int, Webmaster> $includedWebmasters Вебмастера, которые должны получать SMS
 * @property-read Collection<int, SmsClick> $smsClicks Клики по SMS
 * @property-read null|Sms $relatedSms Связанная SMS (для SMS AfterClick - после клика в какой SMS текущую нужно отправлять)
 *
 * @method static SmsBuilder query()
 *
 * @package App\Models
 *
 * @todo Добавить foreign на showcase_id
 */
class Sms extends Model
{
    /**
     * Шаблон, в рамках которого мы заменяем ссылку.
     */
    public const LINK_TEMPLATE = '{link}';

    /**
     * Шаблон, в рамках которого мы заменяем имя.
     */
    public const NAME_TEMPLATE = '{name}';

    /**
     * Префикс для генерации секретного ключа.
     */
    protected const SECRET_KEY_PREFIX = 'QiSdO5DLqoqsxg7N';

    protected $fillable = [
        'name',
        'type',
        'text',
        'from',
        'delay',
        'link',
        'source_id',
        'link_source_id',
        'is_enabled',
        'registered_after',
        'sms_provider_id',
        'showcase_id',
        'related_sms_id',
    ];

    protected $casts = [
        'registered_after' => 'datetime',
        'type' => SmsTypeEnum::class,
    ];

    /**
     * @param $query
     * @return SmsBuilder
     */
    public function newEloquentBuilder($query): SmsBuilder
    {
        return new SmsBuilder($query);
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sms_user')
            ->using(SmsUser::class);
    }

    /**
     * @return BelongsTo
     */
    public function smsProvider(): BelongsTo
    {
        return $this->belongsTo(SmsProvider::class);
    }

    /**
     * @return BelongsTo
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * @return BelongsToMany
     */
    public function includedWebmasters(): BelongsToMany
    {
        return $this->belongsToMany(Webmaster::class, 'sms_included_webmaster');
    }

    /**
     * @return BelongsToMany
     */
    public function excludedWebmasters(): BelongsToMany
    {
        return $this->belongsToMany(Webmaster::class, 'sms_excluded_webmaster');
    }

    /**
     * @return BelongsTo
     */
    public function linkSource(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'link_source_id');
    }

    /**
     * @return HasMany
     */
    public function smsClicks(): HasMany
    {
        return $this->hasMany(SmsClick::class);
    }

    /**
     * @return BelongsTo
     */
    public function relatedSms(): BelongsTo
    {
        return $this->belongsTo(Sms::class, 'related_sms_id');
    }

    /**
     * Получить секретный ключ для SMS.
     * @param string $parameter
     * @return string
     */
    public function getSecretKey(string $parameter): string
    {
        $secretString = self::SECRET_KEY_PREFIX . $this->id . $parameter;
        $hash = hash('sha256', $secretString);
        return Str::substr($hash, 0, 16);
    }
}
