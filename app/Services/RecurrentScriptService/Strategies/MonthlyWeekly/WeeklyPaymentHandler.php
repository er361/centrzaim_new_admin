<?php

namespace App\Services\RecurrentScriptService\Strategies\MonthlyWeekly;

use App\Jobs\ChargePayment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class WeeklyPaymentHandler extends PaymentHandler
{
    private $logger;
    private $weeklyAmount;

    public function __construct($logger, $weeklyAmount)
    {
        $this->logger = $logger;
        $this->weeklyAmount = $weeklyAmount;
    }

    protected function check(User $user): bool
    {
        $weekIntervalInMinutes = config('payments_miazaim.weekly.interval_in_minutes');

        $lastWeeklyPayment = $user->payments()
            ->where('type', Payment::TYPE_RECURRENT)
            ->where('subtype', Payment::SUBTYPE_WEEKLY)
            ->orderBy('created_at', 'desc')
            ->first();

        $retryPayment = $user->payments()
            ->where('type', Payment::TYPE_RECURRENT)
            ->where('subtype', Payment::SUBTYPE_RETRY_AFTER_FAILED_MONTHLY)
            ->first();

        $timeConditionPass = match (true) {
            $lastWeeklyPayment !== null => $lastWeeklyPayment
                ->created_at
                ->lt(Carbon::now()->subMinutes($weekIntervalInMinutes)),
            $retryPayment !== null => $retryPayment
                ->created_at
                ->lt(Carbon::now()->subMinutes($weekIntervalInMinutes)),
            default => (function () use ($user) {
                Log::error('No retry no weekly payment found', ['user_id' => $user->id]);
                return false;
            })(),
        };

        $this->logger->info('Проверка на прошедшую неделю', [
            'user_id' => $user->id,
            'timeConditionPass' => $timeConditionPass,
        ]);

        // Используем интервал в минутах для проверки, прошла ли нужная неделя
        if ($timeConditionPass) {
            dispatch(new ChargePayment($this->weeklyAmount, $user, Payment::SUBTYPE_WEEKLY))
                ->onQueue('payments');
            return true;
        }

        return false;
    }
}
