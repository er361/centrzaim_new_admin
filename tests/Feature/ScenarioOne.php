<?php

use App\Jobs\ChargePayment;
use App\Models\Payment;
use App\Models\User;
use App\Services\RecurrentScriptService\Strategies\MonthlyWeekly\MonthlyWeeklyStrategy;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use function Pest\Laravel\get;

// Очистка таблиц перед каждым тестом
beforeEach(function () {
    User::query()->delete();
    Payment::query()->delete();
    Queue::fake(); // Подменяем очередь

    // Подменяем конфигурацию для тестов
    Config::set('payments.monthly_weekly.monthly.amount', 1998);
    Config::set('payments.monthly_weekly.weekly.amount', 499);
    Config::set('payments.monthly_weekly.retry.amount', 499);

    // Настройка интервалов в минутах, но для тестов используем дни
    $monthInMinutes = 60 * 24 * 30; // 30 дней в минутах
    $weekInMinutes = 60 * 24 * 7;   // 7 дней в минутах
    $dayInMinutes = 60 * 24;        // 1 день в минутах

    Config::set('payments.monthly_weekly.monthly.interval_in_minutes', $monthInMinutes);
    Config::set('payments.monthly_weekly.weekly.interval_in_minutes', $weekInMinutes);
    Config::set('payments.monthly_weekly.retry.interval_in_minutes', $dayInMinutes);

    // Общие настройки
    Config::set('payments.monthly_weekly.common_props.first_payment_delay', 30); // 30 минут
});

// Шаг 1: Первичная оплата при оформлении заявки
it('пытается списать 1998 руб при первичном оформлении подписки', function () {
    $user = User::factory()->create();

    // Запускаем сценарий
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что была попытка списать месячный тариф
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 1998);
});

// Шаг 2: Если списание 1998 руб невозможно, пытаемся списать 499 руб
it('пытается списать 499 руб если не удалось списать 1998 руб', function () {
    $user = User::factory()->create();

    // Создаем отклоненный платеж на 1998 руб
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_DECLINED,
        'amount' => 1998,
        'created_at' => now(),
    ]);

    // Запускаем сценарий
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что была попытка списать недельный тариф
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 499);
    // И не было повторной попытки списать месячный
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 1998);
});

// Шаг 3.1: Месячный цикл (каждые 30 дней)
it('списывает 1998 руб каждые 30 дней в случае успешного месячного платежа', function () {
    $user = User::factory()->create();

    // Создаем успешный месячный платеж 30 дней назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 1998,
        'created_at' => now()->subDays(30),
    ]);

    // Запускаем сценарий
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что была попытка списать месячный тариф
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 1998);
});

it('не списывает месячный платеж если с момента последнего прошло менее 30 дней', function () {
    $user = User::factory()->create();

    // Создаем успешный месячный платеж 15 дней назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 1998,
        'created_at' => now()->subDays(15),
    ]);

    // Запускаем сценарий
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что не было попыток списать ни один из тарифов
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 1998);
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 499);
});

// Шаг 3.2: Недельный цикл (каждые 7 дней)
it('списывает 499 руб каждые 7 дней в случае успешного недельного платежа', function () {
    $user = User::factory()->create();

    // Создаем успешный недельный платеж 7 дней назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 499,
        'created_at' => now()->subDays(7),
    ]);

    // Запускаем сценарий
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что была попытка списать недельный тариф
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 499);
});

it('не списывает недельный платеж если с момента последнего прошло менее 7 дней', function () {
    $user = User::factory()->create();

    // Создаем успешный недельный платеж 3 дня назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 499,
        'created_at' => now()->subDays(3),
    ]);

    // Запускаем сценарий
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что не было попыток списать недельный тариф
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 499);
});

// Шаг 4: Ежедневные попытки при неудачном недельном платеже
it('пытается списать 499 руб каждый день если не удалось списать еженедельный платеж', function () {
    $user = User::factory()->create();

    // Создаем отклоненный недельный платеж вчера
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_DECLINED,
        'amount' => 499,
        'created_at' => now()->subDay(),
    ]);

    // Запускаем сценарий
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что была попытка списать недельный тариф
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 499);
});

it('пытается списать 499 руб каждый день если последний успешный недельный платеж был более 7 дней назад', function () {
    $user = User::factory()->create();

    // Создаем успешный недельный платеж 8 дней назад
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 499,
        'created_at' => now()->subDays(8),
    ]);

    // И отклоненный платеж вчера
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_DECLINED,
        'amount' => 499,
        'created_at' => now()->subDay(),
    ]);

    // Запускаем сценарий
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что была попытка списать недельный тариф
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 499);
});

// Переход между тарифами
it('переходит на еженедельный цикл после успешного списания 499 руб', function () {
    $user = User::factory()->create();

    // Создаем отклоненный месячный платеж
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_DECLINED,
        'amount' => 1998,
        'created_at' => now()->subDays(2),
    ]);

    // Успешный недельный платеж после этого
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 499,
        'created_at' => now()->subDay(),
    ]);

    // Запускаем сценарий
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что не было попыток списать ни один из тарифов (так как успешный платеж был вчера)
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 1998);
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 499);
});

// Краевые случаи
it('игнорирует платежи с другими суммами', function () {
    $user = User::factory()->create();

    // Создаем успешный платеж с нестандартной суммой
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_PAYED,
        'amount' => 1000, // Не 1998 и не 499
        'created_at' => now(),
    ]);

    // Запускаем сценарий
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что была попытка списать недельный тариф (так как нет другой валидной подписки)
    Queue::assertPushed(fn (ChargePayment $job) => $job->amount === 499);
});

it('не создает дубликатов платежей если запустить сценарий несколько раз в один день', function () {
    $user = User::factory()->create();

    // Создаем отклоненный недельный платеж
    $user->payments()->create([
        'type' => Payment::TYPE_RECURRENT,
        'status' => Payment::STATUS_DECLINED,
        'amount' => 499,
        'created_at' => now()->subDays(2),
    ]);

    // Запускаем сценарий первый раз
    $MonthlyWeeklyStrategy = new MonthlyWeeklyStrategy();
    $MonthlyWeeklyStrategy->charge($user);

    // Очищаем очередь
    Queue::fake();

    // Запускаем сценарий второй раз в тот же день
    $MonthlyWeeklyStrategy->charge($user);

    // Проверяем, что не было повторной попытки списать
    Queue::assertNotPushed(fn (ChargePayment $job) => $job->amount === 499);
});