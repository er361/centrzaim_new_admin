<?php

namespace Database\Factories;

use App\Models\UserOffer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserOfferFactory extends Factory
{
    protected $model = UserOffer::class;

    public function definition(): array
    {
        return [
            'repeated_offers' => [
                $this->faker->randomNumber(2),
                $this->faker->randomNumber(2),
                $this->faker->randomNumber(2),
                $this->faker->randomNumber(2)
            ],
            'user_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
