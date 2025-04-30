<?php

namespace App\Models;

use App\Builders\PostbackBuilder;
use Carbon\CarbonInterface;
use Database\Factories\PostbackFactory;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id Идентификатор записи
 * @property float $cost Стоимость
 * @property null|CarbonInterface $sent_at Время отправки конверсии (или null, если произошла ошибка при отправке)
 * @property int $user_id Идентификатор пользователя
 * @property null|string $remote_user_id Отправленный в ПП идентификатор пользователя
 * @property-read User $user Пользователь
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 *
 * @method static PostbackBuilder query()
 * @method static PostbackFactory factory()
 */
class Postback extends Model
{
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'cost',
        'user_id',
        'sent_at',
        'remote_user_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * @param $query
     * @return PostbackBuilder
     */
    public function newEloquentBuilder($query): PostbackBuilder
    {
        return new PostbackBuilder($query);
    }

    /**
     * @return BelongsTo<User, Postback>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
