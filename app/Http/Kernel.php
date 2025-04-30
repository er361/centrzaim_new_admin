<?php

namespace App\Http;

use App\Http\Middleware\NeedToActivate;
use App\Http\Middleware\NeedToFill;
use App\Http\Middleware\PaymentRequired;
use App\Http\Middleware\RouteLoggerMiddleware;
use App\Http\Middleware\ShareDashboardPageTagMiddleware;
use App\Http\Middleware\UserIsAdministrator;
use App\Http\Middleware\VitrinaMiddleware;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, string>
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        // \Kyranb\Footprints\Middleware\CaptureAttributionDataMiddleware::class, // TODO Fix it later
        AddQueuedCookiesToResponse::class,

    ];

    /**
     * The application's route middleware groups.
     *
     * @var  array<string, array<int, string>>
     */
    protected $middlewareGroups = [
        'web' => [
            Middleware\EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            Middleware\VerifyCsrfToken::class,
            SubstituteBindings::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Добавлен Sanctum middleware
            'throttle:180,1',
            'bindings',
        ],

        'dashboard' => [
            'auth',
            NeedToActivate::class,
            NeedToFill::class,
            PaymentRequired::class,
            ShareDashboardPageTagMiddleware::class,
        ],

        'admin' => [
            UserIsAdministrator::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, string>
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'auth.sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Добавлен как отдельный middleware
    ];

    protected $middlewarePriority = [
        VitrinaMiddleware::class,
        'auth',
        'dashboard',
    ];
}