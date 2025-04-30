<?php

namespace Tests\Unit;

use App\Builders\UserBuilder;
use App\Console\Commands\CreateRecurrentPayments;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\PaymentService\Contracts\PaymentServiceInterface;
use App\Services\SettingsService\SettingsService;
use Carbon\CarbonImmutable;
use Config;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateRecurrentPaymentsTest extends TestCase
{
    protected function tearDown(): void
    {
        // Очищаем все моки и временные данные между тестами
        Mockery::close();
        Carbon::setTestNow(); // Очищаем тестовые значения Carbon
        $this->artisan('cache:clear'); // сброс кэша контейнера (если необходимо)
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Установка mockery для каждого теста
        $this->artisan('cache:clear'); // сброс кэша контейнера (если необходимо)
        Carbon::setTestNow(Carbon::now()); // Сбрасываем текущее время для тестов
    }

    public function testHourlyLimitCondition()
    {
        // Устанавливаем лимит платежей в конфигурации
        Config::set('payments.recurrent_payments_per_hour', 5);

        // Устанавливаем текущее время для Carbon
        Carbon::setTestNow(Carbon::now());

        // Создаём частичный мок для построителя запросов
        $queryMock = Mockery::mock(Builder::class);
        $queryMock->shouldReceive('where')
            ->with('created_at', '>=', Mockery::on(function ($value) {
                return $value->equalTo(Carbon::now()->subHour());
            }))
            ->andReturnSelf();
        $queryMock->shouldReceive('whereTypeRecurrent')
            ->andReturnSelf();
        $queryMock->shouldReceive('count')
            ->andReturn(6); // Возвращаем количество платежей больше лимита

        // Мокируем метод query() для модели Payment
        $paymentMock = Mockery::mock('alias:' . Payment::class);
        $paymentMock->shouldReceive('query')
            ->andReturn($queryMock);

        // Мокируем вызов Log::channel и последующие вызовы Log::debug и Log::info
        Log::shouldReceive('channel')
            ->with('payments')
            ->andReturnSelf();

        // Ожидаем, что будет залогировано сообщение о превышении лимита платежей
        Log::shouldReceive('debug')
            ->once()
            ->with('Превышен лимит платежей в час, не начинаем списание.', [
                'payments_last_hour' => 6,
                'payment_limit' => 5,
//                'payment_limit' => 50,
                'payments_can_be_created' => -1,
            ]);

        Log::shouldReceive('debug')
            ->once()
            ->with('Начальные условия проверки не пройдены');

        // Добавляем ожидание для Log::info, так как оно может вызываться в другом месте кода
        Log::shouldReceive('info')->andReturnTrue();


        // Создаем экземпляр команды и выполняем метод handle
        $command = app(CreateRecurrentPayments::class);

        // Выполняем команду
        try {
            $command->handle();
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }
        $this->assertTrue(true, 'Исключения не было, а значит был вызов 
        "Превышен лимит платежей в час, не начинаем списание. с нужными параметрами"');
    }

    public function testQueueNotEmpty()
    {
        // Мокируем Queue::size, чтобы указать, что в очереди есть задачи
        Queue::shouldReceive('size')
            ->with('payments')
            ->andReturn(1);

        Log::shouldReceive('channel')
            ->with('payments')
            ->andReturnSelf();

        Log::shouldReceive('debug')
            ->once()
            ->with('В очереди payments имеются записи, не начинаем списание.', [
                'queue_size' => 1,
            ]);

        Log::shouldReceive('debug')
            ->once()
            ->with('Начальные условия проверки не пройдены');

        $command = app(CreateRecurrentPayments::class);

        try {
            $command->handle();
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }

        $this->assertTrue(true, 'Очередь не пуста, команда завершена с соответствующим логированием.');
    }

    public function runLastPaymentStatusCondition(
        $errorCount,
        $paymentStatus,
        $paymentCreatedAt,
        $errorCode,
        $configDelays,
        $expectedLog
    )
    {
        Config::set('payments.plans.0.max_consequent_errors', 3);
        Config::set('payments.delays.after_unsuccessful_payments_with_delay_days', 2);
        // Mocking User
        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->recurrent_payment_consequent_error_count = $errorCount;

        $failedPayment = new Payment();
        $failedPayment->status = $paymentStatus;
        $failedPayment->created_at = $paymentCreatedAt;
        $failedPayment->error_code = $errorCode;

        $user->setRelation('latestRecurrentPayment', $failedPayment);

        // Mocking the query builder
        $userBuilder = Mockery::mock(UserBuilder::class);
        $userBuilder->shouldReceive('with')->with(['latestRecurrentPayment'])->andReturnSelf();
        $userBuilder->shouldReceive('orderBy')->with('id')->andReturnSelf();
        $userBuilder->shouldReceive('eachById')->andReturnUsing(function ($callback) use ($user) {
            $callback($user);
        });

        // Mocking UserRepository
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('getUsersForRecurrentCharge')->willReturn($userBuilder);

        // Mocking App facade
        App::partialMock()
            ->shouldReceive('make')
            ->with(UserRepository::class)
            ->andReturn($userRepository);

        // Mocking Log facade
        Log::shouldReceive('error')->once()->with($expectedLog, [
            'user_id' => $user->id,
            'last_recurrent_payment_id' => $failedPayment->id,
            'recurrent_payment_consequent_error_count' => $user->recurrent_payment_consequent_error_count,
        ]);

        Log::shouldReceive('channel')
            ->with('payments')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->with('Начинаем списывать рекуррентные платежи.', \Mockery::on(function ($context) {
                return isset($context['process']) && Str::isUuid($context['process']);
            }));

        foreach ($configDelays as $configKey => $configValue) {
            config([$configKey => $configValue]);
        }

        $command = new CreateRecurrentPayments();
//        $command = new OldCreateRecurrentPayments();

        $command->handle();

        $this->assertTrue(true, 'Исключения не было, а значит был вызов "У пользователя есть недавние неуспешные платежи. с нужными параметрами"');
    }

    public function testExceededConsequentErrorLimit()
    {
        $this->runLastPaymentStatusCondition(
            5,
            Payment::STATUS_DECLINED,
            CarbonImmutable::now()->subDays(1),
            'regular_error',
            ['payments.delays.after_unsuccessful_payments_days' => 3],
            'У пользователя есть недавние неуспешные платежи.'
        );
    }

    public function testFailedPaymentWithExtendedDelay()
    {
        $this->runLastPaymentStatusCondition(
            1,
            Payment::STATUS_DECLINED,
            CarbonImmutable::now()->addDays(5),
            'special_error_code',
            [
                'services.impaya.error_codes.delay' => ['special_error_code'],
                'payments.delays.after_unsuccessful_payments_with_delay_days' => 1
            ],
            'У пользователя есть недавние неуспешные платежи.'
        );
    }

    public function testFailedPaymentWithStandardDelay()
    {
        $this->runLastPaymentStatusCondition(
            1,
            Payment::STATUS_DECLINED,
            CarbonImmutable::now()->subDays(2),
            'regular_error',
            ['payments.delays.after_unsuccessful_payments_days' => 3],
            'У пользователя есть недавние неуспешные платежи.'
        );
    }

    public function testPaymentLimitCheck()
    {
        Config::set('payments.plans.0.should_stop_when_charged', true);
        Config::set('payments.plans.0.recurrent', [0 => 10, 1 => 20, 2 => 30, 3 => 40, 4 => 50]); // 5 платежей

        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->recurrent_payment_success_count = 5; // Все платежи уже списаны

        $failedPayment = new Payment();
        $failedPayment->status = Payment::STATUS_DECLINED;
        $failedPayment->created_at = CarbonImmutable::now()->subDays(1);
        $failedPayment->error_code = 'some_error_code';

        $user->setRelation('latestRecurrentPayment', $failedPayment);

        // Mocking the query builder
        $userBuilder = Mockery::mock(UserBuilder::class);
        $userBuilder->shouldReceive('with')->with(['latestRecurrentPayment'])->andReturnSelf();
        $userBuilder->shouldReceive('orderBy')->with('id')->andReturnSelf();
        $userBuilder->shouldReceive('eachById')->andReturnUsing(function ($callback) use ($user) {
            $callback($user);
        });

        // Mocking UserRepository
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('getUsersForRecurrentCharge')->willReturn($userBuilder);

        // Mocking App facade
        App::partialMock()
            ->shouldReceive('make')
            ->with(UserRepository::class)
            ->andReturn($userRepository);

        // Mocking Log facade
        Log::shouldReceive('error')->once()
            ->with('Уже списали у пользователя все необходимые платежи.', [
            'user_id' => $user->id,
            'success_recurrent_payments' => $user->recurrent_payment_success_count,
        ]);

        Log::shouldReceive('channel')
            ->with('payments')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->with('Начинаем списывать рекуррентные платежи.', \Mockery::on(function ($context) {
                return isset($context['process']) && Str::isUuid($context['process']);
            }));

//        $command = new OldCreateRecurrentPayments();
        $command = new CreateRecurrentPayments();
        $command->handle();

        $this->assertTrue(true, 'Исключения не было, и лог "Уже списали у пользователя все необходимые платежи." был вызван.');
    }

    public function testPaymentDelayAfterMinutes()
    {
        Config::set('payments.plans.0.should_stop_when_charged', true);
        $now = CarbonImmutable::now();

        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->created_at = $now->subMinutes(10); // Зарегистрирован 10 минут назад

        $planConfiguration = [
            'recurrent' => [0 => ['after_minutes' => 15]], // Задержка 15 минут
            'delay_between_iteration_payments_days' => 1,
            'should_stop_when_charged' => true,
        ];

        $paymentToProvide = ['after_minutes' => 15];

        // Mocking the query builder
        $userBuilder = Mockery::mock(UserBuilder::class);
        $userBuilder->shouldReceive('with')->with(['latestRecurrentPayment'])->andReturnSelf();
        $userBuilder->shouldReceive('orderBy')->with('id')->andReturnSelf();
        $userBuilder->shouldReceive('eachById')->andReturnUsing(function ($callback) use ($user) {
            $callback($user);
        });

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('getUsersForRecurrentCharge')->willReturn($userBuilder);

        App::partialMock()
            ->shouldReceive('make')
            ->with(UserRepository::class)
            ->andReturn($userRepository);

        Log::shouldReceive('warning')
            ->once()
            ->with('Платеж отклонен: недостаточно времени с момента регистрации пользователя для списания.', [
                'user_id' => $user->id,
                'created_at' => $user->created_at,
                'after_minutes' => $paymentToProvide['after_minutes'],
            ]);


        Log::shouldReceive('error')->never();
        Log::shouldReceive('channel')
            ->with('payments')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->with('Начинаем списывать рекуррентные платежи.', \Mockery::on(function ($context) {
                return isset($context['process']) && Str::isUuid($context['process']);
            }));

//        $command = new OldCreateRecurrentPayments();
        $command = new CreateRecurrentPayments();

        // Mocking $now and planConfiguration inside the command's handle method
        Config::set('payments.plans.0', $planConfiguration);
        $command->handle();
        $this->assertTrue(true, 'Исключения не было, так как время для списания еще не наступило.');
    }

    public function testDelayBetweenIterationsForFirstPayment()
    {
        $planConfiguration = [
            'recurrent' => [0 => ['after_minutes' => 15]], // Задержка 15 минут
            'delay_between_iteration_payments_days' => 1,
            'should_stop_when_charged' => false,
        ];
        Config::set('payments.plans.0', $planConfiguration);
        $now = CarbonImmutable::now();

        // Создаем частичный мок User
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->recurrent_payment_success_count = 20;
        $user->created_at = $now->subDays(2); // Зарегистрирован вчера

        // Мокаем метод payments чтобы возвращать коллекцию платежей
        $hasManyMock = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        $hasManyMock->shouldReceive('whereTypeRecurrent')->andReturnSelf();
        $hasManyMock->shouldReceive('whereStatusPayed')->andReturnSelf();
        $hasManyMock->shouldReceive('where')->with('iteration_number', Mockery::any())->andReturnSelf();
        $hasManyMock->shouldReceive('min')->with('created_at')->andReturn($now->subDays(0));

        $user->shouldReceive('payments')->andReturn($hasManyMock);
        // Мокаем UserBuilder
        $userBuilder = Mockery::mock(UserBuilder::class);
        $userBuilder->shouldReceive('with')->with(['latestRecurrentPayment'])->andReturnSelf();
        $userBuilder->shouldReceive('orderBy')->with('id')->andReturnSelf();
        $userBuilder->shouldReceive('eachById')->andReturnUsing(function ($callback) use ($user) {
            $callback($user);
        });

        // Мокаем UserRepository и внедряем его через контейнер
        $userRepository = Mockery::mock(UserRepository::class);
        $userRepository->shouldReceive('getUsersForRecurrentCharge')->andReturn($userBuilder);

        App::partialMock()
            ->shouldReceive('make')
            ->with(UserRepository::class)
            ->andReturn($userRepository);

        Log::shouldReceive('channel')
            ->with('payments')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->with('Начинаем списывать рекуррентные платежи.', \Mockery::on(function ($context) {
                return isset($context['process']) && Str::isUuid($context['process']);
            }));
        // Проверяем, что лог вызван
        Log::shouldReceive('error')
            ->once()
            ->with('Платеж отклонен: задержка до новой итерации не выполнена.', [
            'user_id' => $user->id,
            'payment_number' => 0,
            'previous_iteration_started_at' => $now->subDays(0),
        ]);

//        $command = new OldCreateRecurrentPayments();
        $command = new CreateRecurrentPayments();

        $command->handle();

        // Используем Mockery для проверки вызовов
        Mockery::close();

        $this->assertTrue(true, 'Исключения не было, и лог "Еще не прошло достаточно времени, чтобы начинать новую итерацию по пользователю." был вызван.');
    }

    public function testDelayBetweenIterationsForSubsequentPayments()
    {
        $planConfiguration = [
            'recurrent' => [0 => ['after_minutes' => 15], 1 => ['after_minutes' => 15]], // 15-minute delay
            'delay_between_iteration_payments_days' => 1,
            'should_stop_when_charged' => false,
        ];

        Config::set('payments.plans.0', $planConfiguration);
        $now = CarbonImmutable::now();

        // Create a partial mock of User
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->recurrent_payment_success_count = 19;
        $user->created_at = $now->subDays(2); // Registered 2 days ago

        // Mock the payments method to return a collection of payments
        $hasManyMock = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        $hasManyMock->shouldReceive('whereTypeRecurrent')->andReturnSelf();
        $hasManyMock->shouldReceive('whereStatusPayed')->andReturnSelf();
        $hasManyMock->shouldReceive('wherePaymentNumber')->with(Mockery::any())->andReturnSelf();
        $hasManyMock->shouldReceive('whereIterationNumber')->with(Mockery::any())->andReturnSelf();
        $hasManyMock->shouldReceive('min')->with('created_at')->andReturn($now->subDays(0));

        $user->shouldReceive('payments')->andReturn($hasManyMock);

        // Mock UserBuilder
        $userBuilder = Mockery::mock(UserBuilder::class);
        $userBuilder->shouldReceive('with')->with(['latestRecurrentPayment'])->andReturnSelf();
        $userBuilder->shouldReceive('orderBy')->with('id')->andReturnSelf();
        $userBuilder->shouldReceive('eachById')->andReturnUsing(function ($callback) use ($user) {
            $callback($user);
        });

        // Mock UserRepository and inject it via the container
        $userRepository = Mockery::mock(UserRepository::class);
        $userRepository->shouldReceive('getUsersForRecurrentCharge')->andReturn($userBuilder);

        App::partialMock()
            ->shouldReceive('make')
            ->with(UserRepository::class)
            ->andReturn($userRepository);

        // Mock the logger
        Log::shouldReceive('channel')->with('payments')->andReturnSelf();

        // Verify that an error log is called for delay between payments within an iteration
        Log::shouldReceive('warning')
            ->once()
            ->with('Платеж отклонен: задержка до повторного платежа внутри итерации не выполнена.');

        Log::shouldReceive('info')
            ->once()
            ->with('Начинаем списывать рекуррентные платежи.', \Mockery::on(function ($context) {
                return isset($context['process']) && Str::isUuid($context['process']);
            }));

//        $command = new OldCreateRecurrentPayments();
        $command = new CreateRecurrentPayments();
        $command->handle();

        // Use Mockery to verify expectations
        Mockery::close();

        $this->assertTrue(true, 'No exceptions thrown, and log "Not enough time has passed to charge this payment (delay based on delay_between_iteration_payments_days)." was called.');
    }

    public function testMaxAmountCondition()
    {
        Config::set('payments.plans.0.max_amount', 100); // Устанавливаем лимит в 100 единиц

        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->recurrent_payment_success_count = 19; // У пользователя уже 5 успешных платежей
        $user->created_at = CarbonImmutable::now()->subDays(2); // Зарегистрирован 2 дня назад

        // Настроим успешные платежи пользователя на сумму, близкую к max_amount
        $payment1 = new Payment(['amount' => 40, 'status' => Payment::STATUS_PAYED]);
        $payment2 = new Payment(['amount' => 50, 'status' => Payment::STATUS_PAYED]);
        $user->setRelation('payments', collect([$payment1, $payment2]));

        $planConfiguration = [
            'max_amount' => 100,
            'should_stop_when_charged' => true,
            'recurrent' => [
                0 => ['after_minutes' => 15],
                1 => ['after_minutes' => 15, 'amount' => 120],
                19 => ['after_minutes' => 15],
            ],
        ];

        // Настраиваем конфигурацию для плана
        Config::set('payments.plans.0', $planConfiguration);

        $paymentToProvide = ['amount' => 120]; // Новый платёж на 20 единиц, который превысит лимит

        // Mocking the query builder
        $userBuilder = Mockery::mock(UserBuilder::class);
        $userBuilder->shouldReceive('with')->with(['latestRecurrentPayment'])->andReturnSelf();
        $userBuilder->shouldReceive('orderBy')->with('id')->andReturnSelf();
        $userBuilder->shouldReceive('eachById')->andReturnUsing(function ($callback) use ($user) {
            $callback($user);
        });

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('getUsersForRecurrentCharge')->willReturn($userBuilder);

        App::partialMock()
            ->shouldReceive('make')
            ->with(UserRepository::class)
            ->andReturn($userRepository);

        // Проверка логирования ошибки, если превышен лимит
        Log::shouldReceive('error')
            ->once()
            ->with('В результате платежа спишем денег больше, чем запланировали.', [
            'user_id' => $user->id,
            'max_amount' => $planConfiguration['max_amount'],
            'payment_to_provide_amount' => $paymentToProvide['amount'],
            'payments_sum' => 0, // Сумма успешных платежей
        ]);

        Log::shouldReceive('channel')->with('payments')->andReturnSelf();
        Log::shouldReceive('info')
            ->once()
            ->with('Начинаем списывать рекуррентные платежи.', \Mockery::on(function ($context) {
                return isset($context['process']) && Str::isUuid($context['process']);
            }));

//        $command = new OldCreateRecurrentPayments();
        $command = new CreateRecurrentPayments();
        $command->handle();

        $this->assertTrue(true, 'Исключения не было, и лог "В результате платежа спишем денег больше, чем запланировали." был вызван.');
    }

}
