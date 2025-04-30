<?php

namespace App\Providers;

use App\ViewComposers\ConfigViewComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', ConfigViewComposer::class);
    }
}
