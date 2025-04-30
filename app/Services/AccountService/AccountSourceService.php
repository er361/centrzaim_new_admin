<?php

namespace App\Services\AccountService;

use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use Illuminate\Support\Facades\Auth;

class AccountSourceService
{
    /**
     * Получить источник текущего пользователя.
     * @return Source|null
     */
    public static function getSource(): ?Source
    {
        /** @var null|User $user */
        $user = Auth::user();

        if ($user === null) {
            $webmaster = self::getWebmasterFromCookie();
        } else {
            $webmaster = $user->webmaster;
        }

        return $webmaster?->source;
    }

    /**
     * Получить вебмастера текущего пользователя.
     * @return Webmaster|null
     */
    public static function getWebmaster(): ?Webmaster
    {
        /** @var null|User $user */
        $user = Auth::user();

        if ($user === null) {
            return self::getWebmasterFromCookie();
        }

        return $user->webmaster;
    }

    /**
     * Получить webmaster из cookie.
     * @return Webmaster|null
     */
    protected static function getWebmasterFromCookie(): ?Webmaster
    {
        $webmasterId = request()->cookie('webmaster_id');

        if ($webmasterId === null) {
            return null;
        }

        /** @var null|Webmaster $webmaster */
        $webmaster = Webmaster::query()->find($webmasterId);
        return $webmaster;
    }
}