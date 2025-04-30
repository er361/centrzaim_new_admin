<?php

use App\Jobs\ChargePayment;
use App\Models\Payment;
use App\Models\User;
use App\Services\RecurrentScriptService\Strategies\Scenario2\Scenario2;
use function Pest\Laravel\get;

// Очистка таблиц перед каждым тестом
beforeEach(function () {
    User::query()->delete();
    Payment::query()->delete();
    Queue::fake(); // Подменяем очередь
});

// Первый день - первая попытка через 10 минут
it('пытается списать 299 руб через 10 минут после оформления заявки', function () {
    $user = User::factory()->create(['created_at' => now()->subMinutes(10)]);

    // Запускаем сценарий
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что была попытка списать 299 руб
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 299);
});

it('не пытается списать платеж если прошло менее 10 минут после регистрации', function () {
    $user = User::factory()->create(['created_at' => now()->subMinutes(5)]);

    // Запускаем сценарий
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что не было попыток списать
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 299);
});

// Повторная попытка через 120 минут после неудачной
it('пытается списать 299 руб через 120 минут после неудачной попытки', function () {
    $user = User::factory()->create(['created_at' => now()->subMinutes(130)]);

    // Создаем неудачную попытку 120 минут назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_DECLINED,
        'amount' => 299,
        'created_at' => now()->subMinutes(120),
    ]);

    // Запускаем сценарий
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что была повторная попытка списать 299 руб
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 299);
});

it('не пытается списать платеж если после неудачной попытки прошло менее 120 минут', function () {
    $user = User::factory()->create(['created_at' => now()->subMinutes(70)]);

    // Создаем неудачную попытку 60 минут назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_DECLINED,
        'amount' => 299,
        'created_at' => now()->subMinutes(60),
    ]);

    // Запускаем сценарий
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что не было попыток списать
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 299);
});

// Последующие платежи через 7 дней
it('списывает 299 руб каждые 7 дней в случае успешного платежа', function () {
    $user = User::factory()->create();

    // Создаем успешный платеж 7 дней назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 299,
        'created_at' => now()->subDays(7),
    ]);

    // Запускаем сценарий
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что была попытка списать 299 руб
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 299);
});

it('не списывает платеж если с момента последнего успешного прошло менее 7 дней', function () {
    $user = User::factory()->create();

    // Создаем успешный платеж 5 дней назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 299,
        'created_at' => now()->subDays(5),
    ]);

    // Запускаем сценарий
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что не было попыток списать
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 299);
});

// Проверка приоритета успешных платежей
it('учитывает только успешные платежи для расчета 7-дневного интервала', function () {
    $user = User::factory()->create();

    // Создаем успешный платеж 8 дней назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 299,
        'created_at' => now()->subDays(8),
    ]);

    // Создаем неудачный платеж 2 дня назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_DECLINED,
        'amount' => 299,
        'created_at' => now()->subDays(2),
    ]);

    // Запускаем сценарий
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что была попытка списать 299 руб (так как 7 дней прошло с успешного платежа)
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 299);
});

// Комплексный тест
it('правильно обрабатывает сложный сценарий с несколькими попытками', function () {
    $user = User::factory()->create(['created_at' => now()->subDays(2)]);

    // Создаем историю платежей
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_DECLINED,
        'amount' => 299,
        'created_at' => now()->subDays(2)->addMinutes(10), // Первая неудачная попытка
    ]);

    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_DECLINED,
        'amount' => 299,
        'created_at' => now()->subDays(2)->addMinutes(130), // Вторая неудачная попытка
    ]);

    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 299,
        'created_at' => now()->subDays(1), // Успешная попытка
    ]);

    // Запускаем сценарий
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что не было попыток списать (так как успешный платеж был меньше 7 дней назад)
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 299);
});

// Краевые случаи
it('игнорирует платежи с другими суммами', function () {
    $user = User::factory()->create(['created_at' => now()->subMinutes(10)]);

    // Создаем успешный платеж с другой суммой
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 499, // Не 299
        'created_at' => now(),
    ]);

    // Запускаем сценарий
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что была попытка списать 299 руб (так как другие суммы игнорируются)
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 299);
});

it('обрабатывает случай когда нет истории платежей', function () {
    $user = User::factory()->create(['created_at' => now()->subMinutes(10)]);

    // Запускаем сценарий
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что была попытка списать 299 руб (первый платеж)
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 299);
});

it('не создает дубликатов при многократном запуске в один день', function () {
    $user = User::factory()->create(['created_at' => now()->subMinutes(10)]);

    // Запускаем сценарий первый раз
    $scenario2 = new Scenario2();
    $scenario2->charge($user);

    // Проверяем, что была попытка списать
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 299);

    // Очищаем очередь
    Queue::fake();

    // Запускаем сценарий второй раз
    $scenario2->charge($user);

    // Проверяем, что не было повторной попытки
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 299);
});