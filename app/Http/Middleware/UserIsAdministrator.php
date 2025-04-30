<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserIsAdministrator
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        /** @var User $user */
        $user = Auth::user();

        if (!in_array($user->role_id, [Role::ID_ADMIN, Role::ID_CONTACT_CENTER, Role::ID_TRAFFIC_SOURCE, Role::ID_SUPER_ADMIN], true)) {
            abort(404);
        }

        return $next($request);
    }
}