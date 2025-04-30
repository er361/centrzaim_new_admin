<?php

namespace App\Http\Middleware;

use App\Services\AccountService\AccountSourceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SharePageTagMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $webmaster = AccountSourceService::getWebmaster();
        View::share('webmasterPageTag', $webmaster?->page_tag);

        return $next($request);
    }
}