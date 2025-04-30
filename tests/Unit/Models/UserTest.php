<?php

namespace Tests\Unit\Models;

use App\Models\Payment;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Проверка, что находит последний рекурретный платеж, если он последний.
     * @return void
     */
    public function testLatestRecurrentPaymentIfLast(): void
    {
        $user = User::factory()->createOne();

        $defaultPayment = Payment::factory()
            ->for($user)
            ->typeDefault()
            ->createOne();

        $recurrentPayment = Payment::factory()
            ->for($user)
            ->typeRecurrent()
            ->createOne();

        self::assertTrue(
            $recurrentPayment->is($user->latestRecurrentPayment)
        );
    }

    /**
     * Проверка, что находит последний рекурретный платеж, если последний платеж не рекуррентный.
     * @return void
     */
    public function testLatestRecurrentPaymentIfNotLast(): void
    {
        $user = User::factory()->createOne();

        $recurrentPayment = Payment::factory()
            ->for($user)
            ->typeRecurrent()
            ->createOne();

        $defaultPayment = Payment::factory()
            ->for($user)
            ->typeDefault()
            ->createOne();

        self::assertTrue(
            $recurrentPayment->is($user->latestRecurrentPayment)
        );
    }

    /**
     * Проверка, что находит последний рекурретный платеж, если последний платеж не рекуррентный.
     * @return void
     */
    public function testLatestRecurrentIterationStartPayment(): void
    {
        $user = User::factory()->createOne();

        // Убираем нужный платеж в начало, чтобы если сработает фильтр только по latest, нашло не его
        $paymentNumbers = [1, 0];
        $types = [Payment::TYPE_RECURRENT, Payment::TYPE_DEFAULT];
        $statuses = [Payment::STATUS_PAYED, Payment::STATUS_DECLINED];

        foreach ($types as $type) {
            foreach ($paymentNumbers as $paymentNumber) {
                foreach ($statuses as $status) {
                    Payment::factory()
                        ->for($user)
                        ->type($type)
                        ->status($status)
                        ->paymentNumber($paymentNumber)
                        ->createOne();
                }

            }
        }

        $latestRecurrentIterationStartPayment = $user->latestRecurrentIterationStartPayment;

        self::assertNotNull($latestRecurrentIterationStartPayment);
        self::assertEquals(0, $latestRecurrentIterationStartPayment->payment_number);
        self::assertEquals(Payment::TYPE_RECURRENT, $latestRecurrentIterationStartPayment->type);
        self::assertEquals(Payment::STATUS_PAYED, $latestRecurrentIterationStartPayment->status);
    }
}