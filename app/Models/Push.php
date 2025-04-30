<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Push extends Model
{
	protected $fillable = ['name', 'text', 'delay', 'enabled', 'url', 'visits'];
	protected $hidden = [];


}
