<?php


namespace App\Services\PostbackService\Providers;


use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;

class DirectPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        // Nothing
    }
}