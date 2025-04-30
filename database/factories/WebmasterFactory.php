<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Webmaster;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebmasterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Webmaster::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'api_id' => $this->faker->randomNumber(6, true),
        ];
    }
}
