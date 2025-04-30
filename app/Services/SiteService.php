<?php

namespace App\Services;

use Illuminate\Support\Arr;

class SiteService
{
    public static function getActiveSiteConfiguration(): array {
        $activeSite = config('sites.active');
        return Arr::get(config('sites'), $activeSite, config('sites.default'));
    }

}