<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\UserProfileService
 */
class UserProfileService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\UserProfileService::class;
    }
}
