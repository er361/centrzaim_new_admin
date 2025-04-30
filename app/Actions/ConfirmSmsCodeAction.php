<?php

namespace App\Actions;


use App\Models\SmsCode;
use App\Models\User;
use App\Services\ActivationService\ActivationServiceInterface;
use Illuminate\Support\Carbon;
use Sf7kmmr\SmsService\Jobs\SendSmsJob;

class ConfirmSmsCodeAction
{
    public function run(User $user, string $code): bool
    {
        if ($user->mphone === null) {
            return false;
        }

        if ($code != $user->activation_code) {
            return false;
        }

        $user->is_active = true;
        $user->activation_code = null;
        $user->save();
        return true;
    }


}
