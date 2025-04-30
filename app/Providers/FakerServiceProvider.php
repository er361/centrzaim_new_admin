<?php

namespace App\Providers;

use App\Faker\MobilePhoneProvider;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\ServiceProvider;

class FakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->environment('production')) {
            $this->app->singleton(Generator::class, function () {
                $faker = Factory::create();
                $faker->addProvider(new MobilePhoneProvider($faker));
                return $faker;
            });
        }

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Nothing
    }
}
