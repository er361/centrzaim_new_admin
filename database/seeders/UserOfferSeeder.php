<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserOffer;
use Illuminate\Database\Seeder;

class UserOfferSeeder extends Seeder
{
    public function run(): void
    {
        UserOffer::factory()->count(10)->create([
            'user_id' => User::inRandomOrder()->first()->id,
        ]);
    }
}
