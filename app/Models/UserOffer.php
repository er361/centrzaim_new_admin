<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 *
 * @property array $repeated_offers
 */
class UserOffer extends Model
{
    use HasFactory;

    protected $casts = [
        'repeated_offers' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'repeated_offers',
    ];

    public function setRepeatedOffersAttribute(array $value): void
    {
        $this->attributes['repeated_offers'] = json_encode($value);
    }
}
