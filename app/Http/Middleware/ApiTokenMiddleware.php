<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class ApiTokenMiddleware
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
        $token = $request->headers->get('X-TOKEN');

        if ($token === null) {
            abort(Response::HTTP_BAD_REQUEST);
        }

        $expectedToken = config('app.api_token');

        if ($expectedToken === null) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($token !== $expectedToken) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
