<?php

namespace App\Services\AutocompliteService;

use App\Services\AutocompliteService\Commands\ExtractNameFromFile;
use Illuminate\Support\ServiceProvider;

class AutocompliteProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        // Загрузка маршрутов
        $this->loadRoutesFrom(  __DIR__ . '/Routes/api.php');

        // Загрузка миграций
        $this->loadMigrationsFrom(__DIR__ . '/Migrations/2024_06_27_105341_create_full_name_helpers_table.php');
        $this->commands([
            ExtractNameFromFile::class
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
