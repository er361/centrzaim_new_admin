<?php

namespace App\Builders;


use App\Models\Action;
use App\Models\Role;
use App\Models\User;
use App\Models\Webmaster;
use Illuminate\Database\Eloquent\Model;


/**
 * @template TModelClass of Model&Action
 *
 * @extends BaseBuilder<Action>
 */
class ActionBuilder extends BaseBuilder
{
    /**
     * @param User $user
     * @return ActionBuilder
     */
    public function forUser(User $user): ActionBuilder
    {
        if ($user->role_id === Role::ID_ADMIN || $user->isSuperAdmin()) {
            return $this;
        }

        if ($user->role_id !== Role::ID_TRAFFIC_SOURCE) {
            return $this->whereRaw('0 = 1');
        }

        $webmasterQuery = Webmaster::query()->forUser($user)->select('id');
        return $this->whereIn('webmaster_id', $webmasterQuery);
    }
}