<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Services\AccessService;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class UserPolicy
{
    use HandlesAuthorization;

    public function show(User $user, User $targetUser): bool
    {
        if (!Gate::allows('user_view')) {
            return false;
        }

        if (Gate::allows('user_full_access')) {
            return true;
        }

        if ($user->role_id === Role::ID_CONTACT_CENTER) {
            return Cache::has(AccessService::getUserAccessCacheKey($user, $targetUser));
        }

        return $user->accessibleWebmasters()->where('id', $targetUser->webmaster_id)->exists();
    }

    public function unsubscribe(User $user, User $targetUser): bool
    {
        if (!Gate::allows('user_unsubscribe')) {
            return false;
        }

        if (Gate::allows('user_full_access')) {
            return true;
        }

        return Cache::has(AccessService::getUserAccessCacheKey($user, $targetUser));
    }
}
