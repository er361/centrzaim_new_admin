<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 *
 * @package App
 * @property string $title
 */
class Role extends Model
{
    use HasFactory;

    /**
     * Идентфикатор роли "Адмиинистратор".
     */
    public const ID_ADMIN = 1;

    const ID_SUPER_ADMIN = 100;

    /**
     * Идентфикатор роли "Пользователь".
     */
    public const ID_USER = 2;

    /**
     * Идентфикатор роли "Сотрудник КЦ".
     */
    public const ID_CONTACT_CENTER = 3;
    /**
     * Идентфикатор роли "Арбитраж трафика".
     */
    public const ID_TRAFFIC_SOURCE = 4;

    protected $fillable = [
        'title'
    ];

    protected $hidden = [];
}
