<?php

namespace App\Http\Middleware;

use App\Services\SiteService;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;

class ThemeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $config = SiteService::getActiveSiteConfiguration();
        $path = Arr::get($config, 'views_path');
        View::addLocation($path);

        return $next($request);
    }
}
