<?php

namespace App\Builders;

use App\Models\BannerStatistic;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModelClass of Model&BannerStatistic
 *
 * @extends BaseBuilder<BannerStatistic>
 */
class BannerStatisticBuilder extends BaseBuilder
{
    /**
     * @param User $user
     * @return BannerStatisticBuilder
     */
    public function forUser(User $user): BannerStatisticBuilder
    {
        if ($user->role_id === Role::ID_ADMIN || $user->isSuperAdmin()) {
            return $this;
        }

        if ($user->role_id !== Role::ID_TRAFFIC_SOURCE) {
            return $this->whereRaw('0 = 1');
        }

        $this->whereHas('webmaster', function (WebmasterBuilder $query) use ($user) {
            $query->forUser($user);
        });

        return $this;
    }
}