<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebmasterTemplate extends Model
{
    protected $fillable = [
        'source_id',
        'showcase_id',
        'webmaster_id',
    ];

    public function loanOffer()
    {
        return $this->hasMany(LoanOffer::class, 'source_id', 'source_id')
            ->where('showcase_id', $this->showcase_id)
            ->where('webmaster_id', $this->webmaster_id);
    }
}
