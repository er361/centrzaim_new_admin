<?php

namespace App\Services\RecurrentScriptService\Strategies\MonthlyWeekly;

use App\Jobs\ChargePayment;
use App\Models\Payment;
use App\Models\User;
use App\Services\RecurrentScriptService\PaymentService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RetryPaymentHandler extends PaymentHandler
{
    private $logger;
    private int $retryAmount;

    public function __construct($logger, $retryAmount)
    {
        $this->logger = $logger;
        $this->retryAmount = $retryAmount;
    }

    protected function check(User $user): bool
    {
        $today = Carbon::today();
        $retryIntervalInMinutes = config('payments_miazaim.retry.interval_in_minutes');

        // Находим последний неуспешный месячный платёж за сегодня
        $latestMonthlyPayment = $user->payments()
            ->where('type', Payment::TYPE_RECURRENT)
            ->where('subtype', Payment::SUBTYPE_MONTHLY)
            ->where('status', Payment::STATUS_DECLINED)
            ->whereDate('created_at', $today)
            ->first();

        if ($latestMonthlyPayment) {
            // Проверяем, был ли уже платёж retry за сегодня
            $lastRetryPayment = $user->payments()
                ->where('type', Payment::TYPE_RECURRENT)
                ->where('subtype', Payment::SUBTYPE_RETRY_AFTER_FAILED_MONTHLY)
                ->orderByDesc('created_at')
                ->first();

            $retryIntervalPassed = $latestMonthlyPayment
                ->created_at
                ->lt(Carbon::now()->subMinutes($retryIntervalInMinutes));

            $this->logger->debug('Проверка на retry', [
                'user_id' => $user->id,
                'last_monthly_payment' => $latestMonthlyPayment->id,
                'last_payment_created_at' => $latestMonthlyPayment->created_at,
                'compare_time_to_pass' => Carbon::now()->subMinutes($retryIntervalInMinutes),
                'last_retry_payment' => $lastRetryPayment?->id,
                'retry_interval_passed' => $retryIntervalPassed,
            ]);

            // Если retry не был выполнен сегодня и интервал для retry прошёл
            if (!$lastRetryPayment && $retryIntervalPassed) {
                PaymentService::createPayment($this->retryAmount, $user, Payment::SUBTYPE_RETRY_AFTER_FAILED_MONTHLY);
                return true;
            }

            // Если нет retry, то нужно остановить цепочку, и ждать пока не придет время для retry
            if (!$lastRetryPayment) {
                Log::info('Ждем пока не придет время для retry', [
                    'user_id' => $user->id,
                    'retry_interval' => $retryIntervalInMinutes,
                    'last_monthly_payment' => $latestMonthlyPayment->id,
                ]);
                return true;
            }
        }

        return false;
    }
}
