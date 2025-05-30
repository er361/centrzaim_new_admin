<?php

namespace Repositories\UserRepository;

use App\Models\Payment;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserRepositoryErrorPaymentsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        \Artisan::call('db:truncate');
    }

    protected function createUserWithPayment(
        int    $planId,
        string $paymentType,
        string $paymentStatus,
        Carbon $createdAt,
        int $paymentAmount,
        int $subType = 0
    )
    {
        // Создаем пользователя с указанными атрибутами
        $user = User::factory()->create(array_merge([
            'payment_plan' => $planId,
            'is_active' => true,
            'is_disabled' => false,
        ],[]));

        // Создаем платеж с заданными параметрами для данного пользователя
        Payment::factory()->create([
            'user_id' => $user->id,
            'type' => $paymentType,
            'status' => $paymentStatus,
            'service' => Payment::SERVICE_IMPAYA,
            'rebill_id' => '123',
            'subtype' =>  $subType,
            'created_at' => $createdAt,
            'amount' => $paymentAmount,
        ]);

        return $user;
    }

    public function test_it_returns_only_users_with_failed_payments()
    {
        $planId = 0;
        $monthlyAmount = config('payments_miazaim.monthly.amount'); // Сумма месячного платежа из конфигурации

        // Пользователь с неуспешным платежом с суммой месячного платежа
        $userWithFailedPayment = $this->createUserWithPayment(
            $planId,
            Payment::TYPE_RECURRENT,
            Payment::STATUS_DECLINED,
            Carbon::now()->subWeek(),
            $monthlyAmount,
            Payment::SUBTYPE_MONTHLY
        );

        // Пользователь с успешным платежом с суммой месячного платежа (не должен попасть в выборку)
        $userWithSuccessfulPayment = $this->createUserWithPayment(
            $planId,
            Payment::TYPE_RECURRENT,
            Payment::STATUS_PAYED,
            Carbon::now()->subMonth(),
            $monthlyAmount,
            Payment::SUBTYPE_MONTHLY
        );

        // Пользователь без платежей (не должен попасть в выборку)
        $userWithoutPayments = User::factory()->create([
            'payment_plan' => $planId,
            'is_active' => true,
            'is_disabled' => false,
        ]);

        // Добавляем карту для каждого пользователя, чтобы они попали в первоначальную выборку
        $usersForTest = collect([
            $userWithFailedPayment,
            $userWithSuccessfulPayment,
            $userWithoutPayments,
        ]);

        $usersForTest->each(function ($user) {
            Payment::factory()->create([
                'user_id' => $user->id,
                'type' => Payment::TYPE_DEFAULT,
                'status' => Payment::STATUS_CARD_ADDED,
                'service' => Payment::SERVICE_IMPAYA,
                'rebill_id' => '123',
                'created_at' => Carbon::now(),
            ]);
        });

        // Шаг 1: Проверка базового запроса
        $repository = new UserRepository();
        $initialUsers = $repository->getUsersForRecurrentCharge($planId)->get();

        // Убедиться, что базовый запрос включает всех пользователей
        $this->assertCount($usersForTest->count(), $initialUsers);
        $usersForTest->each(function ($user) use ($initialUsers) {
            $this->assertTrue($initialUsers->contains($user), "User {$user->id} is missing in initial query.");
        });

        // Шаг 2: Проверка финального фильтра
        $filteredUsers = $repository->getUsersWithErrors($planId)->get();
        // Проверка, что в финальной выборке остался только пользователь с неуспешным платежом и суммой месячного платежа
        $this->assertCount(1, $filteredUsers);
        $this->assertTrue($filteredUsers->contains($userWithFailedPayment));

        // Проверка, что остальные пользователи не попали в финальную выборку
        $this->assertFalse($filteredUsers->contains($userWithSuccessfulPayment));
        $this->assertFalse($filteredUsers->contains($userWithoutPayments));
    }

}