<?php

namespace App\Builders;

use App\Models\Role;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @method null|Source find($id, $columns = ['*'])
 * @template TModelClass of Model&Source
 *
 * @extends BaseBuilder<Source>
 */
class SourceBuilder extends BaseBuilder
{
    /**
     * Фильтр источников, доступных пользователю.
     * @param User $user
     * @return SourceBuilder
     */
    public function forUser(User $user): SourceBuilder
    {
        if ($user->role_id === Role::ID_ADMIN || $user->isSuperAdmin()) {
            return $this;
        }

        if ($user->role_id !== Role::ID_TRAFFIC_SOURCE) {
            return $this->whereRaw('0 = 1');
        }

        return $this->where('id', Source::ID_DIRECT);
    }
}