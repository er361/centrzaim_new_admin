<?php

namespace App\Services\AutocompliteService\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FullNameHelper extends Model
{

    const TYPE_LAST_NAME = 'last_name';
    const TYPE_FIRST_NAME = 'first_name';
    const TYPE_FATHER_NAME = 'father_name';

    protected $fillable = [
        'type',
        'value',
        'gender'
    ];
}
