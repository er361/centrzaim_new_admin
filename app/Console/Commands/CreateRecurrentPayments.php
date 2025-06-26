<?php

namespace App\Console\Commands;

use App\Jobs\ChargePayment;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\PaymentService\Contracts\PaymentServiceInterface;
use App\Services\PaymentService\Contracts\PayRecurrent;
use App\Services\RecurrentScriptService\PaymentService;
use App\Services\SettingsService\SettingsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class CreateRecurrentPayments extends Command
{
    /**
     * Размер чанка для выборки данных из базы.
     */
    protected const int CHUNK_SIZE = 100;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:create-recurrent';
    private int $limit;
    private string $iterationUuid;
    private LoggerInterface $logger;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Списывает деньги с пользователей согласно настройкам списания.';

    public function __construct()
    {
        parent::__construct();
        $this->logger = Log::channel('payments');
        $this->iterationUuid = (string)Str::uuid();
        $this->limit = $this->getLimitToCreatePayments();
    }


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (!$this->checkInitialConditions()) {
            $this->logger->debug('Начальные условия проверки не пройдены');
            return;
        }

        if ($this->limit <= 0) {
            $this->logger->debug('Превышено количество платежей в час', ['limit' => $this->limit]);
            return;
        }


        $this->logger->info('Начинаем списывать рекуррентные платежи.', [
            'process' => $this->iterationUuid,
        ]);

        if (config('app.env') === 'production') {
            sleep(ChargePayment::TIMEOUT);
        }

        $usersForRecurrentCharge = App::make(UserRepository::class)->getUsersForRecurrentCharge();
        $usersForRecurrentCharge->each(fn(User $user)=> (new PaymentService())->processCharge($user));

        $this->logger->info('Завершили списывать рекуррентные платежи.', [
            'process' => $this->iterationUuid,
        ]);
    }

    public function checkInitialConditions(): bool
    {
        // Проверка на включение системы платежей и поддержку рекуррентных платежей
        if (!SettingsService::isPaymentsEnabled()) {
            Log::debug('Платежная система отключена, не списываем рекуррентные платежи.');
            return false;
        }

        $service = app()->make(PaymentServiceInterface::class);
        if (!$service instanceof PayRecurrent) {
            $this->error('Сервис оплаты не поддерживает рекуррентные платежи.');
            Log::critical('Сервис оплаты не поддерживает рекуррентные платежи.');
            return false;
        }

        return true;
    }

    private function getLimitToCreatePayments(): int
    {
        // Skip database queries in testing environment
        if (app()->environment('testing')) {
            return 0;
        }
        
        $paymentLimit = config('payments.recurrent_payments_per_hour');
        $paymentsLastHour = Payment::query()
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->whereTypeRecurrent()
            ->count();

        $limit = $paymentLimit - $paymentsLastHour;
        return $limit;
    }

}
