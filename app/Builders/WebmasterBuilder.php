<?php

namespace App\Builders;

use App\Models\Role;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use Illuminate\Database\Eloquent\Model;

/**
 * @method null|Webmaster find($id, $columns = ['*'])
 * @method null|Webmaster first($columns = ['*'])
 * @method Webmaster firstOrCreate(array $attributes = [], array $values = [])
 *
 * @template TModelClass of Model&Webmaster
 *
 * @extends BaseBuilder<Webmaster>
 */
class WebmasterBuilder extends BaseBuilder
{
    /**
     * Фильтр вебмастеров, доступных пользователю.
     * @param User $user
     * @return WebmasterBuilder
     */
    public function forUser(User $user): WebmasterBuilder
    {
        if ($user->role_id === Role::ID_ADMIN || $user->isSuperAdmin() || $user->isSuperAdmin()) {
            return $this;
        }

        if ($user->role_id !== Role::ID_TRAFFIC_SOURCE) {
            return $this->whereRaw('0 = 1');
        }

        return $this
            ->where('webmasters.source_id', Source::ID_DIRECT)
            ->whereIn('webmasters.id', $user->accessibleWebmasters()->select('id'));
    }
}