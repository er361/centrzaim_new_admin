<?php

namespace App\Builders;

use App\Models\Postback;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @method PostbackBuilder filter($query, array $input = [], $filter = null)
 *
 * @template TModelClass of Model&Postback
 *
 * @extends BaseBuilder<Postback>
 */
class PostbackBuilder extends BaseBuilder
{
    /**
     * @param User $user
     * @return PostbackBuilder
     */
    public function forUser(User $user): PostbackBuilder
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