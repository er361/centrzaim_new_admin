<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUnsubscribeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(\Auth::user()?->is_disabled){
            return redirect()->route('front.index');
        }
        return $next($request);
    }
}
