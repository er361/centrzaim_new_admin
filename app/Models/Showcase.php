<?php

namespace App\Models;

use App\Builders\ShowcaseBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * Class Showcase.
 *
 * @property-read int $id Идентификатор записи
 * @property string $name Название
 * @property null|string $external_url Внешний адрес витрины
 * @property bool $is_public Является ли витрина публично доступной
 *
 * @property-read Collection|LoanOffer[] $loanOffers Займы на витрине
 * @property-read Collection|SourceShowcase[] $sourceShowcases Модели витрин для источников
 *
 * @method static ShowcaseBuilder query()
 *
 * @package App\Models
 */
class Showcase extends Model
{
    use HasFactory;

    /**
     * Размещение в аккаунте пользователя.
     * @deprecated
     */
    public const ID_PRIVATE = 1;

    /**
     * Размещение на публичной витрине.
     * @deprecated
     */
    public const ID_PUBLIC = 2;

    /**
     * Размещение на витрине RZaem.
     * @deprecated
     */
    public const ID_RZAEM = 3;

    /**
     * Размещение на витрине 3aimi.
     * @deprecated
     */
    public const ID_3AIMI = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'is_public',
        'external_url',
    ];

    /**
     * @param $query
     * @return ShowcaseBuilder
     */
    public function newEloquentBuilder($query): ShowcaseBuilder
    {
        return new ShowcaseBuilder($query);
    }

    /**
     * @return HasMany
     */
    public function loanOffers(): HasMany
    {
        return $this->hasMany(LoanOffer::class);
    }

    /**
     * @return HasMany
     */
    public function sourceShowcases(): HasMany
    {
        return $this->hasMany(SourceShowcase::class);
    }
}
