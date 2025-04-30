<?php


namespace Database\Factories;

use App\Models\Postback;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @method Postback createOne($attributes = [])
 */
class PostbackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Postback::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'cost' => $this->faker->randomNumber(3, true),
            'user_id' => User::factory(),
            'sent_at' => $this->faker->dateTimeThisMonth,
            'remote_user_id' => function (array $attributes) {
                return User::query()->find($attributes['user_id'])->unique_id;
            },
        ];
    }
}
