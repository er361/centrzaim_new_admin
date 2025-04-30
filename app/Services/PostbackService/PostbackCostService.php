<?php

namespace App\Services\PostbackService;

use App\Models\User;

class PostbackCostService
{
    /**
     * Получить стоимость для переданного пользователя.
     * @param User $user
     * @return float|null
     */
    public function getCost(User $user): ?float
    {
        $user->loadMissing('webmaster.source');

        if ($user->webmaster === null) {
            return null;
        }

        if ($user->webmaster->postback_cost !== null) {
            return $user->webmaster->postback_cost;
        }

        if ($user->webmaster->source === null) {
            return null;
        }

        if ($user->webmaster->source->postback_cost !== null) {
            return $user->webmaster->source->postback_cost;
        }

        return null;
    }
}