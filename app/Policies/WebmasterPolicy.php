<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\Role;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebmasterPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Webmaster $webmaster): bool
    {
        if ($user->role_id === Role::ID_ADMIN || $user->isSuperAdmin()) {
            return true;
        }

        if ($user->role_id !== Role::ID_TRAFFIC_SOURCE) {
            return false;
        }

        return $user->accessibleWebmasters()->where('id', $webmaster->id)->exists();
    }
}
