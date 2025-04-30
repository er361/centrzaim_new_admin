<?php


namespace App\Services\ActivationService;


use App\Models\User;

interface ActivationServiceInterface
{
    public function sendCode(User $user): void;

    public function resendCode(User $user): void;

    public function validateCode(User $user, string $providedCode): bool;
}