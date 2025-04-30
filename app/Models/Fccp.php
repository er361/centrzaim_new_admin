<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fccp extends Model
{
    protected $table = 'fccps_info';

    protected $fillable = [
        'info',
        'user_id',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public function setInfoAttribute(array $value)
    {
        $this->attributes['info'] = json_encode($value);
    }
}
