<?php

namespace App\Models;

use App\Builders\LoanOfferBuilder;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class LoanOffer.
 *
 * @property-read int $id Идентификатор записи
 * @property int $priority Приоритет в списке
 * @property int $showcase_id Витрина для размещения. Immutable
 * @property int $source_id Партнерская программа, для пользователей которой показываем ссылку
 * @property int $loan_link_id Ссылка для размещения
 * @property int $loan_id  Описание предложения
 * @property bool $is_hidden Скрыто ли предложение на витрине
 * @property int | null $webmaster_id Идентификатор вебмастера
 *
 * @property-read Showcase $showcase Витрина для отображения предложения
 * @property-read Source $source Партнерская программа, для пользователей которой показываем ссылку
 * @property-read LoanLink $loanLink Ссылка для размещения
 * @property-read Loan $loan Описание предложения
 * @property-read SourceShowcase $sourceShowcase Витрина, на котором выбран всплывающим оффером. todo Нет всплывающего оффера!
 *
 * @method static LoanOfferBuilder query()
 *
 * @package App\Models
 */
class LoanOffer extends Model implements Sortable
{
    use SortableTrait;
    use HasFactory;
    use Filterable;
    use SoftDeletes;

    public array $sortable = [
        'order_column_name' => 'priority',
        'sort_when_creating' => true,
    ];

    protected $fillable = [
        'priority',
        'showcase_id',
        'description',
        'loan_id',
        'source_id',
        'loan_link_id',
        'is_hidden',
        'is_backup',
        'webmaster_id',
    ];

    /**
     * @param $query
     * @return LoanOfferBuilder
     */
    public function newEloquentBuilder($query): LoanOfferBuilder
    {
        return new LoanOfferBuilder($query);
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
    public function loanLink(): BelongsTo
    {
        return $this->belongsTo(LoanLink::class);
    }

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
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Витрина, на котором выбран всплывающим оффером.
     * @return HasOne
     */
    public function sourceShowcase(): HasOne
    {
        return $this->hasOne(SourceShowcase::class);
    }

    /**
     * @return Builder
     */
    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('showcase_id', $this->showcase_id)
            ->where('source_id', $this->source_id)
            ->where('webmaster_id', $this->webmaster_id);
    }

    /**
     * Получить ссылку для перехода.
     * @param array $additionalParameters
     * @return string
     */
    public function getShowLink(array $additionalParameters): string
    {
        return route('front.loan-offers.show', array_merge(
            ['loanOffer' => $this,],
            $additionalParameters
        ));
    }
}
