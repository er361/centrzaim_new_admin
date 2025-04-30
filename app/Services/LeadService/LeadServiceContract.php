<?php

namespace App\Services\LeadService;

use App\Models\User;

interface LeadServiceContract
{
    /**
     * Отправляет информацию о пользователе.
     * @param User $user
     * @return void
     */
    public function send(User $user): void;
}