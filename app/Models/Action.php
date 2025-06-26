<?php

namespace App\Models;

use App\Builders\ActionBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Action
 * @package App\Models
 *
 * @property int $webmaster_id
 * @property string $ip
 * @property string $user_agent
 * @property string $api_transaction_id
 * @property string|null $site_id
 * @property string|null $place_id
 * @property string|null $banner_id
 * @property string|null $campaign_id
 *
 * @method static ActionBuilder query()
 */
class Action extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'webmaster_id',
        'ip',
        'user_agent',
        'api_transaction_id',
        'site_id',
        'place_id',
        'banner_id',
        'campaign_id'
    ];

    /**
     * @param $query
     * @return ActionBuilder
     */
    public function newEloquentBuilder($query): ActionBuilder
    {
        return new ActionBuilder($query);
    }

    /**
     * @return BelongsTo
     */
    public function webmaster(): BelongsTo
    {
        return $this->belongsTo(Webmaster::class);
    }
}
