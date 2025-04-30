<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ShareDashboardPageTagMiddleware
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
        /** @var User $user */
        $user = Auth::user();
        $hasSuccessPayments = $user->payments()->whereCardAdded()->exists();

        // Если успешных платежей не было, обнуляем переменную для View
        if (!$hasSuccessPayments) {
            View::share('webmasterPageTag', null);
        }

        return $next($request);
    }
}