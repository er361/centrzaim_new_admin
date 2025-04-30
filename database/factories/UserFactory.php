<?php


namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @method User createOne($attributes = [])
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make($this->faker->password),
            'last_name' => $this->faker->name,
            'middlename' => $this->faker->name,
            'ip_address' => $this->faker->ipv4,
            'mphone' => '+7999' . $this->faker->randomNumber(7, true),
            'transaction_id' => Str::random(),
        ];
    }
}
