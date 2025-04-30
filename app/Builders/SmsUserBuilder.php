<?php

namespace App\Builders;


use App\Models\Role;
use App\Models\SmsUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;


/**
 * @template TModelClass of Model&SmsUser
 *
 * @extends BaseBuilder<SmsUser>
 */
class SmsUserBuilder extends BaseBuilder
{
    /**
     * @param User $user
     * @return SmsUserBuilder
     */
    public function forUser(User $user): SmsUserBuilder
    {
        if ($user->role_id === Role::ID_ADMIN || $user->isSuperAdmin()) {
            return $this;
        }

        if ($user->role_id !== Role::ID_TRAFFIC_SOURCE) {
            return $this->whereRaw('0 = 1');
        }

        $this->whereHas('user', function (UserBuilder $query) use ($user) {
            $query->whereHas('webmaster', function (WebmasterBuilder $query) use ($user) {
                $query->forUser($user);
            });
        });

        return $this;
    }
}