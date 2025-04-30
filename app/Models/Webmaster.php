<?php

namespace App\Models;

use App\Builders\WebmasterBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Webmaster.
 *
 * @property-read int $id
 * @property int $source_id
 * @property int $api_id
 * @property null|float $postback_cost Стоимость конверсии
 * @property string $page_tag HTML код для вставки на страницы сайта
 * @property string $comment Комментарий
 * @property bool $is_payment_required Показывать ли форму оплаты пользователю
 * @property null|int $income_percent Процент заработка вебмастеру
 * @property null|string $postback_step Шаг отправки постбэка для вебмастера

 * Relations:
 * @property-read Source $source
 *
 * Виртуальные свойства:
 * @property-read string $completeName
 *
 * @method static WebmasterBuilder query()
 *
 * @package App\Models
 */
class Webmaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'api_id',
        'postback_cost',
        'page_tag',
        'comment',
        'is_payment_required',
        'income_percent',
        'postback_step',
    ];

    protected $casts = [
        'is_payment_required' => 'bool',
        'income_percent' => 'int',
    ];

    /**
     * @param $query
     * @return WebmasterBuilder
     */
    public function newEloquentBuilder($query): WebmasterBuilder
    {
        return new WebmasterBuilder($query);
    }

    /**
     * @return HasMany
     */
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    public function loanOffers()
    {
        return $this->hasMany(LoanOffer::class);
    }

    /**
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany
     */
    public function conversions(): HasMany
    {
        return $this->hasMany(Conversion::class);
    }

    /**
     * @return BelongsTo
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Получить полное имя вебмастера (с указанием ПП).
     * @return string
     */
    public function getCompleteNameAttribute(): string
    {
        return $this->source->name . ' / ' . $this->api_id;
    }
}
