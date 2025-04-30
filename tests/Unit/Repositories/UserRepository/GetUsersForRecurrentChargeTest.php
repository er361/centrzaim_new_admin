<?php

namespace Tests\Unit\Repositories\UserRepository;

use App\Models\Payment;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class GetUsersForRecurrentChargeTest extends TestCase
{
    /**
     * Проверяем, что выключенные пользователи не попадают в выборку.
     * @dataProvider allPlansDataProvider
     */
    public function testDisabledUsersAreNotIncluded(int $planId): void
    {
        $disabledUser = User::factory()->createOne([
            'is_disabled' => true,
            'payment_plan' => $planId,
        ]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertFalse($builder->where('id', $disabledUser->id)->exists());
    }

    /**
     * Проверяем, что пользователи, которые давно зарегистрированы, не попадают в выборку.
     * @dataProvider notRepeatablePlansDataProvider
     */
    public function testOldUsersAreNotIncluded(int $planId): void
    {
        $user = User::factory()->createOne([
            'payment_plan' => $planId,
        ]);
        $createdAt = Carbon::today()->subDays(config('payments.max_charging_days') + 1);
        $user->setAttribute('created_at', $createdAt);
        $user->save(['timestamps' => false]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertFalse($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что пользователи, которые давно зарегистрированы, не попадают в выборку.
     * @dataProvider allPlansDataProvider
     */
    public function testUsersWithoutCardsNotIncluded(int $planId): void
    {
        $user = User::factory()->createOne([
            'payment_plan' => $planId,
        ]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertFalse($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что пользователи с критичными ошибками не попадают в выборку.
     * @dataProvider allPlansDataProvider
     */
    public function testUsersWithCriticalErrorsNotIncluded(int $planId): void
    {
        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 1,
            'recurrent_payment_success_count' => 0,
        ]);

        Payment::factory()
            ->stateSuccessFullCardAddition()
            ->for($user)
            ->createOne();

        Payment::factory()
            ->for($user)
            ->typeRecurrent()
            ->createOne([
                'service' => Payment::SERVICE_IMPAYA,
                'error_code' => 'ISSUER_FAIL',
            ]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertFalse($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что пользователи, у которых есть недавние неуспешные рекуррентные платежи, не попадают в выборку.
     * @dataProvider allPlansDataProvider
     */
    public function testUsersWithRecentFailedRecurrentNotIncluded(int $planId): void
    {
        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 1,
        ]);

        Payment::factory()->for($user)->createOne([
            'service' => Payment::SERVICE_IMPAYA,
            'status' => Payment::STATUS_CARD_ADDED,
            'type' => Payment::TYPE_DEFAULT,
            'rebill_id' => Str::random()
        ]);

        Payment::factory()->for($user)->createOne([
            'type' => Payment::TYPE_RECURRENT,
            'status' => Payment::STATUS_DECLINED,
        ]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertFalse($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что пользователи, у которых есть недавние неуспешные рекуррентные платежи с ошибкой,
     * требующей увеличения задержки, не попадают в выборку.
     * @dataProvider allPlansDataProvider
     */
    public function testUsersWithRecentFailedRecurrentWithDelayRequiredNotIncluded(int $planId): void
    {
        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 1,
        ]);

        Payment::factory()->for($user)->createOne([
            'service' => Payment::SERVICE_IMPAYA,
            'status' => Payment::STATUS_CARD_ADDED,
            'type' => Payment::TYPE_DEFAULT,
            'rebill_id' => Str::random()
        ]);

        $paymentsCreatedAt = Carbon::now()
            ->subDays(config('payments.delays.after_unsuccessful_payments_days'))
            ->subMinute();

        $payment = Payment::factory()->for($user)->createOne([
            'service' => Payment::SERVICE_IMPAYA,
            'type' => Payment::TYPE_RECURRENT,
            'status' => Payment::STATUS_DECLINED,
            'error_code' => 'ISSUER_LIMIT_FAIL',
        ]);
        $payment->setAttribute('created_at', $paymentsCreatedAt);
        $payment->save(['timestamps' => false]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertFalse($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что пользователи, у которых есть недавние неуспешные рекуррентные платежи,
     * но они не последние, попадают в выборку.
     * @dataProvider allPlansDataProvider
     */
    public function testUsersWithRecentFailedNotLastRecurrentAreIncluded(int $planId): void
    {
        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 0,
            'recurrent_payment_success_count' => 1,
        ]);

        Payment::factory()->for($user)->createOne([
            'service' => Payment::SERVICE_IMPAYA,
            'status' => Payment::STATUS_CARD_ADDED,
            'type' => Payment::TYPE_DEFAULT,
            'rebill_id' => Str::random()
        ]);

        Payment::factory()->for($user)->createOne([
            'type' => Payment::TYPE_RECURRENT,
            'status' => Payment::STATUS_DECLINED,
        ]);

        Payment::factory()->for($user)->createOne([
            'type' => Payment::TYPE_RECURRENT,
            'status' => Payment::STATUS_PAYED,
        ]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertTrue($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что пользователи, у которых есть старые неуспешные рекуррентные платежи, попадают в выборку.
     * @dataProvider allPlansDataProvider
     */
    public function testUsersWithOldFailedRecurrentAreIncluded(int $planId): void
    {
        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 1,
            'recurrent_payment_success_count' => 0,
        ]);

        Payment::factory()->for($user)->createOne([
            'service' => Payment::SERVICE_IMPAYA,
            'status' => Payment::STATUS_CARD_ADDED,
            'type' => Payment::TYPE_DEFAULT,
            'rebill_id' => Str::random()
        ]);

        $payment = Payment::factory()->for($user)->createOne([
            'type' => Payment::TYPE_RECURRENT,
            'status' => Payment::STATUS_DECLINED,
        ]);

        $payment->setAttribute('created_at', now()->subMonths(2));
        $payment->save(['timestamps' => false]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertTrue($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что пользователи, у которых списали все необходимые платежи, не попадают в выборку.
     * @dataProvider notRepeatablePlansDataProvider
     */
    public function testUsersWithAllRecurrentCompletedNotIncluded(int $planId): void
    {
        $expectedPayments = count(config("payments.plans.{$planId}.recurrent"));

        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 0,
            'recurrent_payment_success_count' => $expectedPayments,
        ]);

        Payment::factory()->for($user)->createOne([
            'service' => Payment::SERVICE_IMPAYA,
            'status' => Payment::STATUS_CARD_ADDED,
            'type' => Payment::TYPE_DEFAULT,
            'rebill_id' => Str::random(),
        ]);

        Payment::factory()->for($user)->count($expectedPayments)->create([
            'type' => Payment::TYPE_RECURRENT,
            'status' => Payment::STATUS_PAYED,
        ]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertFalse($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что пользователи, у которых не списали все необходимые платежи, попадают в выборку.
     * @dataProvider notRepeatablePlansDataProvider
     */
    public function testUsersWithNotAllRecurrentCompletedAreIncluded(int $planId): void
    {
        $expectedPayments = count(config("payments.plans.{$planId}.recurrent")) - 1;

        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 0,
            'recurrent_payment_success_count' => $expectedPayments,
        ]);

        Payment::factory()->for($user)->createOne([
            'service' => Payment::SERVICE_IMPAYA,
            'status' => Payment::STATUS_CARD_ADDED,
            'type' => Payment::TYPE_DEFAULT,
            'rebill_id' => Str::random()
        ]);

        Payment::factory()->for($user)->count($expectedPayments)->createOne([
            'type' => Payment::TYPE_RECURRENT,
            'status' => Payment::STATUS_PAYED,
        ]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertTrue($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что пользователи, у которых не списали все необходимые платежи,
     * и не списали больше положеного, попадают в выборку.
     * @dataProvider notRepeatablePlansDataProvider
     */
    public function testUsersWithLargeRecurrentCompletedAreIncluded(int $planId): void
    {
        // Делим сумму равномерно на все платежи, немного оставляем
        $expectedPayments = count(config("payments.plans.{$planId}.recurrent"));
        $conductedPayments = $expectedPayments - 1;
        $expectedRecurrentPaymentsSum = config("payments.plans.{$planId}.max_amount");
        $paymentAmount = floor(($expectedRecurrentPaymentsSum - 1) / $conductedPayments);

        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 0,
            'recurrent_payment_success_count' => $conductedPayments,
        ]);

        Payment::factory()->for($user)->createOne([
            'service' => Payment::SERVICE_IMPAYA,
            'status' => Payment::STATUS_CARD_ADDED,
            'type' => Payment::TYPE_DEFAULT,
            'rebill_id' => Str::random()
        ]);

        Payment::factory()->for($user)->count($conductedPayments)->createOne([
            'type' => Payment::TYPE_RECURRENT,
            'status' => Payment::STATUS_PAYED,
            'amount' => $paymentAmount,
        ]);

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertTrue($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что после завершения текущей итерации списания по пользователям начинаются снова, если прошла задержка.
     * @dataProvider repeatablePlansDataProvider
     */
    public function testUsersStartOverAfterDelay(int $planId): void
    {
        $planConfiguration = config("payments.plans.{$planId}");

        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 0,
            'recurrent_payment_success_count' => count($planConfiguration['recurrent']),
        ]);

        $paymentsCreatedAt = Carbon::now()->subDays($planConfiguration['delay_between_iteration_payments_days']);

        $payment = Payment::factory()->for($user)
            ->stateSuccessFullCardAddition()
            ->createOne();
        $payment->setAttribute('created_at', $paymentsCreatedAt);
        $payment->save(['timestamps' => false]);

        for ($i = 0; $i < count($planConfiguration['recurrent']); $i++) {
            $payment = Payment::factory()->for($user)
                ->typeRecurrent()
                ->statusPayed()
                ->iterationNumber(0)
                ->paymentNumber($i)
                ->createOne();
            $payment->setAttribute('created_at', $paymentsCreatedAt);
            $payment->save(['timestamps' => false]);
        }

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertTrue($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что после завершения текущей итерации списания по пользователям начинаются снова, если прошла задержка.
     * @dataProvider repeatablePlansDataProvider
     */
    public function testPaymentsContinueIfIterationNotFinished(int $planId): void
    {
        $planConfiguration = config("payments.plans.{$planId}");

        $conductedPayments = count($planConfiguration['recurrent']) - 1;

        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 0,
            'recurrent_payment_success_count' => $conductedPayments,
        ]);

        $paymentsCreatedAt = Carbon::now()->subDays($planConfiguration['delay_between_iteration_payments_days']);

        $payment = Payment::factory()->for($user)
            ->stateSuccessFullCardAddition()
            ->createOne();
        $payment->setAttribute('created_at', $paymentsCreatedAt);
        $payment->save(['timestamps' => false]);

        for ($i = 0; $i < $conductedPayments; $i++) {
            $payment = Payment::factory()->for($user)
                ->typeRecurrent()
                ->statusPayed()
                ->iterationNumber(0)
                ->paymentNumber($i)
                ->createOne();
            $payment->setAttribute('created_at', $paymentsCreatedAt);
            $payment->save(['timestamps' => false]);
        }

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertTrue($builder->where('id', $user->id)->exists());
    }

    /**
     * Проверяем, что после завершения текущей итерации списания по пользователям не начинаются снова, если не прошла задержка.
     * @dataProvider repeatablePlansDataProvider
     */
    public function testUsersDoesNotStartOverBeforeDelay(int $planId): void
    {
        $planConfiguration = config("payments.plans.{$planId}");
        $conductedPayments = count($planConfiguration['recurrent']);

        $user = User::factory()->createOne([
            'payment_plan' => $planId,
            'recurrent_payment_consequent_error_count' => 0,
            'recurrent_payment_success_count' => $conductedPayments,
        ]);

        $planConfiguration = config("payments.plans.{$planId}");

        $paymentsCreatedAt = Carbon::now()->subDays($planConfiguration['delay_between_iteration_payments_days'] - 1);

        $payment = Payment::factory()->for($user)
            ->stateSuccessFullCardAddition()
            ->createOne();
        $payment->setAttribute('created_at', $paymentsCreatedAt);
        $payment->save(['timestamps' => false]);

        for ($i = 0; $i < $conductedPayments; $i++) {
            $payment = Payment::factory()->for($user)
                ->typeRecurrent()
                ->statusPayed()
                ->iterationNumber(0)
                ->paymentNumber($i)
                ->createOne();
            $payment->setAttribute('created_at', $paymentsCreatedAt);
            $payment->save(['timestamps' => false]);
        }

        $repository = new UserRepository();
        $builder = $repository->getUsersForRecurrentCharge($planId);

        self::assertFalse($builder->where('id', $user->id)->exists());
    }

    /**
     * Провайдер данных по всем тарифным планам.
     * @return int[][]
     */
    public function allPlansDataProvider(): array
    {
        return [
            [0],
            [1],
            [2],
            [3],
            [7],
        ];
    }

    /**
     * Провайдер данных по тарифным планам без перезапуска.
     * @return int[][]
     */
    public function notRepeatablePlansDataProvider(): array
    {
        return [
            [0],
        ];
    }

    /**
     * Провайдер данных по тарифным планам с перезапуском.
     * @return int[][]
     */
    public function repeatablePlansDataProvider(): array
    {
        return [
            [1],
            [2],
            [3],
            [7],
        ];
    }
}