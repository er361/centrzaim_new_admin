<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method Payment createOne($attributes = [])
 */
class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->randomNumber(2),
            'status' => collect(array_keys(Payment::STATUSES))->random(),
        ];
    }

    /**
     * Тип платежа.
     * @return $this
     */
    public function type(int $type): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Обычный платеж.
     * @return $this
     */
    public function typeDefault(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => Payment::TYPE_DEFAULT,
        ]);
    }

    /**
     * Рекурретный платеж.
     * @return $this
     */
    public function typeRecurrent(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => Payment::TYPE_RECURRENT,
        ]);
    }

    /**
     * Успешная привязка карты.
     * @return $this
     */
    public function stateSuccessFullCardAddition(): static
    {
        return $this->state(fn(array $attributes) => [
            'service' => Payment::SERVICE_IMPAYA,
            'status' => Payment::STATUS_CARD_ADDED,
            'type' => Payment::TYPE_DEFAULT,
            'rebill_id' => Str::random(),
        ]);
    }

    /**
     * Управлением статусом
     * @return $this
     */
    public function status(int $status): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => $status,
        ]);
    }

    /**
     * Платеж отклонен.
     * @return $this
     */
    public function statusDeclined(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Payment::STATUS_DECLINED,
        ]);
    }

    /**
     * Платеж отклонен.
     * @return $this
     */
    public function statusPayed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Payment::STATUS_PAYED,
        ]);
    }

    /**
     * Номер итерации.
     * @return $this
     */
    public function iterationNumber(int $iterationNumber): static
    {
        return $this->state(fn(array $attributes) => [
            'iteration_number' => $iterationNumber,
        ]);
    }

    /**
     * Номер платежа.
     * @return $this
     */
    public function paymentNumber(int $paymentNumber): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_number' => $paymentNumber,
        ]);
    }
}
