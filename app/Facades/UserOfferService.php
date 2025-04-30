<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\UserOfferService
 */
class UserOfferService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\UserOfferService::class;
    }
}
