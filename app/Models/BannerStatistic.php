<?php

namespace App\Models;

use App\Builders\BannerStatisticBuilder;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read  int $id
 * @property CarbonInterface $api_date
 * @property int $banner_id
 * @property int $source_id
 * @property null|int $webmaster_id
 * @property int $impressions
 * @property int $clicks
 * @property float $ctr
 * @property float $revenue
 * @property float $e_cpm
 *
 * @property-read Banner $banner
 * @property-read Source $source
 * @property-read null|Webmaster $webmaster
 *
 * @method static BannerStatisticBuilder query()
 */
class BannerStatistic extends Model
{
    protected $fillable = [
        'api_date',
        'banner_id',
        'source_id',
        'webmaster_id',
        'impressions',
        'clicks',
        'ctr',
        'revenue',
        'e_cpm',
    ];

    protected $casts = [
        'api_date' => 'datetime:Y-m-d',
    ];

    public function newEloquentBuilder($query): BannerStatisticBuilder
    {
        return new BannerStatisticBuilder($query);
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function webmaster(): BelongsTo
    {
        return $this->belongsTo(Webmaster::class);
    }
}
