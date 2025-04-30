<?php

namespace App\Services;

use App\Models\User;


class AccessService
{
    /**
     * Получить ключ для кэширования запроса доступа к пользователю.
     *
     * @param User $currentUser
     * @param User $userToView
     * @return string
     */
    public static function getUserAccessCacheKey(User $currentUser, User $userToView): string
    {
        return 'user_' . $currentUser->id . '_access_' . $userToView->id;
    }
}