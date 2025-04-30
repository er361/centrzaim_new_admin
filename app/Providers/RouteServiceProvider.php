<?php

namespace App\Providers;

use App\Http\Middleware\AdminViewMiddleware;
use App\Http\Middleware\SharePageTagMiddleware;
use App\Http\Middleware\ShareSettingsServiceMiddleware;
use App\Http\Middleware\ThemeMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebFrontRoutes();
        $this->mapWebAdminRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebFrontRoutes(): void
    {
        Route::middleware([
            'web',
            ThemeMiddleware::class,
            SharePageTagMiddleware::class,
            ShareSettingsServiceMiddleware::class,
        ])->group(base_path('routes/web/front.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebAdminRoutes(): void
    {
        Route::middleware([
            'web',
            'auth',
            'admin',
            AdminViewMiddleware::class,
        ])
            ->prefix('admin')
            ->as('admin.')
            ->group(base_path('routes/web/admin.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
