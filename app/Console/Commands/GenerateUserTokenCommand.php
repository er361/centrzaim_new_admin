<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateUserTokenCommand extends Command
{
    protected $signature = 'user:token {email} {name=api-token}';
    protected $description = 'Генерация API токена для пользователя';

    public function handle()
    {
        $email = $this->argument('email');
        $tokenName = $this->argument('name');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Пользователь с email {$email} не найден!");
            return 1;
        }

        // Опционально: удалить существующие токены
        // $user->tokens()->where('name', $tokenName)->delete();

        $token = $user->createToken($tokenName)->plainTextToken;

        $this->info("Токен успешно создан для {$email}:");
        $this->info($token);

        return 0;
    }
}