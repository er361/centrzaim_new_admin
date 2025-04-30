<?php

namespace App\Actions;

use App\Models\User;
use App\Repositories\UserRepository;

class CreateSuccessPaymentAction
{
    public function __construct(
        private UserRepository $userRepository,
        private readonly int   $planId,
    )
    {
    }

    public function run()
    {
        // Получаем список пользователей,
        $userQuery = $this->userRepository->getUsersForRecurrentCharge($this->planId);

        $userQuery->with(['latestRecurrentPayment'])
            ->orderBy('id')
            ->eachById(function (User $user) {

            });

    }
}