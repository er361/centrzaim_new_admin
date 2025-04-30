<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * Class SourceShowcase.
 *
 * @property-read int $id Идентификатор записи
 * @property null|int $showcase_id Идентификатор витрины
 * @property null|int $source_id Идентификатор источника
 * @property null|int $loan_offer_id Идентификатор всплывающего оффера для витрины
 * @property null|int $webmaster_id Идентификатор вебмастера
 *
 * @property-read Showcase $showcase Витрина
 * @property-read Source $source Источник
 * @property-read null|LoanOffer $loanOffer Всплывающий оффер для витрины
 *
 * @package App\Models
 */
class SourceShowcase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'showcase_id',
        'source_id',
        'loan_offer_id',
        'webmaster_id',
    ];

    /**
     * @return BelongsTo
     */
    public function showcase(): BelongsTo
    {
        return $this->belongsTo(Showcase::class);
    }

    public function webmaster(): BelongsTo
    {
        return $this->belongsTo(Webmaster::class);
    }

    /**
     * @return BelongsTo
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * @return BelongsTo
     */
    public function loanOffer(): BelongsTo
    {
        return $this->belongsTo(LoanOffer::class);
    }
}
