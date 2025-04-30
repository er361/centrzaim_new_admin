<?php

namespace App\Models;

use App\Builders\UserBuilder;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


/**
 * @property-read int $id Идентификатор записи
 * @property string $name Название сервиса
 * @property null|CarbonInterface $registered_after Отправлять анкеты, зарегистрированные после
 * @property null|int $delay_minutes Задержка в минутах перед отправкой анкеты
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 *
 * @property-read Collection|User[] $users Отправленные анкеты
 */
class LeadService extends Model
{
    /**
     * Идентификатор QZaem.
     */
    public const ID_Q_ZAEM = 1;

    /**
     * Идентификатор LeadsTech.
     */
    public const ID_LEADS_TECH = 2;

    /**
     * Идентификатор Leads (МигКредит).
     */
    public const ID_LEADS_MIG_CREDIT = 3;

    /**
     * Идентификатор DigitalContact.
     */
    public const ID_DIGITAL_CONTACT = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'registered_after',
        'delay_minutes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registered_after' => 'datetime',
    ];

    /**
     * @return BelongsToMany|UserBuilder
     */
    public function users(): BelongsToMany|UserBuilder
    {
        return $this->belongsToMany(User::class)
            ->withPivot('error_message')
            ->withTimestamps();
    }
}
