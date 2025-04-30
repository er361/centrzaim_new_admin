<?php

namespace App\Builders;

use App\Models\Conversion;
use App\Models\Role;
use App\Models\User;
use App\Models\Webmaster;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModelClass of Model&Conversion
 *
 * @extends BaseBuilder<Conversion>
 */
class ConversionBuilder extends BaseBuilder
{
    /**
     * Фильтр по одобренным конверсиям.
     * @return ConversionBuilder
     */
    public function whereApiStatusApproved(): ConversionBuilder
    {
        return $this->where('api_status', Conversion::STATUS_APPROVED);
    }

    /**
     * @param User $user
     * @return ConversionBuilder
     */
    public function forUser(User $user): ConversionBuilder
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