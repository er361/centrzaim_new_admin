<?php


namespace App\Services\PostbackService;


use App\Models\User;

interface PostbackNotifyServiceInterface
{
    /**
     * Отправка постбэка по пользователю.
     *
     * @param User $user
     */
    public function send(User $user): void;
}