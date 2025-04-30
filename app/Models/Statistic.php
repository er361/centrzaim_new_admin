<?php

namespace App\Models;

use App\Builders\StatisticBuilder;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Statistic.
 *
 * @property-read int $id
 * @property CarbonInterface $date
 * @property int $source_id
 * @property int $webmaster_id
 * @property null|int $webmaster_income_coefficient
 * @property int $actions_count
 * @property int $users_count
 * @property int $active_users_count
 * @property int $card_added_users_count
 * @property float $dashboard_conversions
 * @property float $sms_conversions
 * @property float $sms_cost_sum
 * @property float $payment_sum
 * @property float $banners_sum
 * @property int $postback_count
 * @property float $postback_cost_sum
 * @property float $total
 * @property string $version
 *
 * Relations:
 * @property-read null|Webmaster $webmaster
 *
 * @method static StatisticBuilder query()
 *
 * @package App\Models
 */
class Statistic extends Model
{
    protected $fillable = [
        'date',
        'source_id',
        'webmaster_id',
        'webmaster_income_coefficient',
        'actions_count',
        'users_count',
        'active_users_count',
        'card_added_users_count',
        'dashboard_conversions',
        'sms_conversions',
        'sms_cost_sum',
        'payments_sum',
        'ltv_sum',
        'banners_sum',
        'postback_count',
        'postback_cost_sum',
        'total',
        'version',
    ];

    protected $casts = [
        'date' => 'date',
        'webmaster_income_coefficient' => 'int',
        'actions_count' => 'int',
        'users_count' => 'int',
        'active_users_count' => 'int',
        'card_added_users_count' => 'int',
        'dashboard_conversions' => 'float',
        'sms_conversions' => 'float',
        'sms_cost_sum' => 'float',
        'payments_sum' => 'float',
        'banners_sum' => 'float',
        'postback_count' => 'int',
        'postback_cost_sum' => 'float',
    ];

    public function newEloquentBuilder($query): StatisticBuilder
    {
        return new StatisticBuilder($query);
    }

    public function webmaster(): BelongsTo
    {
        return $this->belongsTo(Webmaster::class);
    }
}
