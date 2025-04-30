<?php

namespace App\Events;

use App\Models\Webmaster;
use Illuminate\Foundation\Events\Dispatchable;

class WebmasterRegistered
{
    use Dispatchable;

    public Webmaster $webmaster;

    public function __construct(Webmaster $webmaster)
    {
        $this->webmaster = $webmaster;
    }
}
