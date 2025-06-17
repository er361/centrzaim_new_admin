<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $user_id
 * @property string $source
 * @property null|string $site_id
 * @property null|string $place_id
 * @property null|string $banner_id
 * @property null|string $campaign_id
 * @property null|string $click_id
 * @property null|string $webmaster_id
 * @property null|array $raw_data
 * @property-read \Carbon\Carbon $created_at
 * @property-read \Carbon\Carbon $updated_at
 * @property-read User $user
 */
class UserExtraData extends Model
{
    use HasFactory;

    protected $table = 'user_extra_data';

    protected $fillable = [
        'user_id',
        'source',
        'site_id',
        'place_id',
        'banner_id',
        'campaign_id',
        'click_id',
        'webmaster_id',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
