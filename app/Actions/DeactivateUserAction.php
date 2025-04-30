<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Str;

class DeactivateUserAction
{
    public function run(User $user): void
    {
            $user->update([
                'is_disabled' => true,
                'unsubscribed_at' => now(),
            ]);
    }
}
