<?php

namespace App\Facades;

use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Facade;

/**
 * @see SettingsService
 */
class SettingsServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SettingsService::class;
    }
}
