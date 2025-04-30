<?php

namespace App\Models;

use App\Builders\SmsClickBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id Идентификатор записи
 * @property int $user_id ID пользователя
 * @property int $sms_id ID SMS
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 *
 * Relations:
 * @property-read User $user
 * @property-read Sms $sms
 *
 * @method static SmsClickBuilder query()
 */
class SmsClick extends Model
{
    protected $fillable = [
        'user_id',
        'sms_id',
    ];

    /**
     * @param $query
     * @return SmsClickBuilder
     */
    public function newEloquentBuilder($query): SmsClickBuilder
    {
        return new SmsClickBuilder($query);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function sms(): BelongsTo
    {
        return $this->belongsTo(Sms::class);
    }
}
