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
        'api_transaction_id'
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
