<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class Loan.
 *
 * @property-read int $id Идентификатор записи
 * @property string $image_path Путь к изображению
 * @property string $name Название
 * @property string $description Описание
 * @property null|float $rating Рейтинг предложения
 * @property null|string $amount Сумма займа
 * @property null|string $issuing_time Время выдачи займа
 * @property null|string $issuing_period Срок выдачи займа
 * @property null|string $issuing_bid Ставка выдачи займа
 * @property null|array $immutable_fields Неизменяемые при обновлении из апи поля
 *
 * @property-read string $image_url Ссылка на изображение
 * @property-read string $user_rating Рейтинг для отображения пользователям
 * @property-read Collection|LoanOffer[] $loanOffers Предложения на витринах
 * @property-read Collection|LoanLink[] $loanLinks Ссылки для предложения
 *
 * @package App\Models
 */
class Loan extends Model
{
    use SoftDeletes;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'immutable_fields' => 'array',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'image_path',
        'name',
        'description',
        'rating',
        'amount',
        'issuing_time',
        'issuing_period',
        'issuing_bid',
        'api_id',
        'license',
        'link',
        'source_id',
        'link_source_id',
        'immutable_fields',
    ];

    /**
     * @return HasMany
     */
    public function loanOffers(): HasMany
    {
        return $this->hasMany(LoanOffer::class);
    }

    public function setApiIssuingBidWith(?string $value): void
    {
        if (in_array('issuing_bid', $this->immutable_fields ?? [])) {
            return;
        }
        $this->attributes['issuing_bid'] = $value ?: config('loan.defaults.issuing_bid');
    }

    public function setApiIssuingPeriodWith(?string $value): void
    {
        if (in_array('issuing_period', $this->immutable_fields ?? [])) {
            return;
        }
        $this->attributes['issuing_period'] = $value ?: config('loan.defaults.issuing_period');
    }

    public function setApiAmountWith(?string $value): void
    {
        if (in_array('amount', $this->immutable_fields ?? [])) {
            return;
        }
        $this->attributes['amount'] = $value ?: config('loan.defaults.sum');
    }

    /**
     * @return HasMany
     */
    public function loanLinks(): HasMany
    {
        return $this->hasMany(LoanLink::class);
    }

    /**
     * Ссылка на изображение.
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }

    /**
     * Получить рейтинг для отображения пользователям.
     *
     * @return string
     */
    public function getUserRatingAttribute(): string
    {
        // Меняем 4.80 и 5.0 до 4.8 и 5., а далее корректируем завершение строки точкой

        $rating = rtrim((string)$this->rating, '0');
        if (Str::endsWith($rating, '.')) {
            $rating = Str::beforeLast($rating, '.');
        }

        return $rating;
    }

}