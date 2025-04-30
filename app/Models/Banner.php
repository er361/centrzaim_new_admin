<?php

namespace App\Models;

use App\Builders\BannerBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $id
 * @property string $name
 * @property string $position
 * @property string $code
 *
 * @property-read Collection<int, Source> $sources
 * @property-read Collection<int, Webmaster> $webmasters
 *
 * @method static BannerBuilder query()
 */
class Banner extends Model
{
    use SoftDeletes;

    public const POSITION_UNSUBSCRIBE_1 = 'unsub_1';
    public const POSITION_UNSUBSCRIBE_2 = 'unsub_2';
    public const POSITION_UNSUBSCRIBE_3 = 'unsub_3';
    public const POSITION_FOOTER = 'footer';
    public const POSITION_MAIN1 = 'main1';
    public const POSITION_MAIN2 = 'main2';
    public const POSITION_MAIN3 = 'main3';
    public const POSITION_MAIN4 = 'main4';
    public const POSITION_MAIN5 = 'main5';
    public const POSITION_ANKETA = 'anketa';
    public const POSITION_PAY = 'pay';
    public const POSITION_VITRINA = 'vitrina';

    public const POSITIONS = [
        self::POSITION_UNSUBSCRIBE_1 => 'unsub_1 (над формой отписки)',
        self::POSITION_UNSUBSCRIBE_2 => 'unsub_2 (под формой отписки)',
        self::POSITION_UNSUBSCRIBE_3 => 'unsub_3 (после отписки)',
        self::POSITION_FOOTER => 'footer (футер)',
        self::POSITION_MAIN1 => 'main1 (главная 1)',
        self::POSITION_MAIN2 => 'main2 (главная 2)',
        self::POSITION_MAIN3 => 'main3 (главная 3)',
        self::POSITION_MAIN4 => 'main4 (главная 4)',
        self::POSITION_MAIN5 => 'main5 (главная 5)',
        self::POSITION_ANKETA => 'anketa (анкета)',
        self::POSITION_PAY => 'pay (привязка карты)',
        self::POSITION_VITRINA => 'vitrina (витрина)',
    ];

    protected $fillable = [
        'name',
        'position',
        'code',
        'placement_id',
    ];

    /**
     * @param $query
     * @return BannerBuilder
     */
    public function newEloquentBuilder($query): BannerBuilder
    {
        return new BannerBuilder($query);
    }

    /**
     * @return BelongsToMany
     */
    public function sources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class);
    }

    /**
     * @return BelongsToMany
     */
    public function webmasters(): BelongsToMany
    {
        return $this->belongsToMany(Webmaster::class);
    }
}
