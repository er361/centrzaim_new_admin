<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VitrinaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $guest = \Auth::guest();
        if($request->is('vitrina') && $guest) {
            return redirect()->route('public.vitrina');
        }
        return $next($request);
    }
}
