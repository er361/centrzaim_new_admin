<?php

namespace App\Services\PaymentConditions;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonImmutable;

class LastPaymentStatusCondition implements PaymentConditionInterface
{
    protected int $delayAfterUnsuccessfulPaymentsDays;
    protected int $delayAfterUnsuccessfulPaymentsWithDelayDays;
    protected array $errorCodesDelay;

    public function __construct()
    {
        $delayAfterUnsuccessfulPaymentsDays = config('payments.delays.after_unsuccessful_payments_days');
        $delayAfterUnsuccessfulPaymentsWithDelayDays = config('payments.delays.after_unsuccessful_payments_with_delay_days');
        $errorCodesDelay = config('services.impaya.error_codes.delay');

        $this->delayAfterUnsuccessfulPaymentsDays = $delayAfterUnsuccessfulPaymentsDays;
        $this->delayAfterUnsuccessfulPaymentsWithDelayDays = $delayAfterUnsuccessfulPaymentsWithDelayDays;
        $this->errorCodesDelay = $errorCodesDelay;
    }

    public function check(User $user, array $planConfiguration, CarbonImmutable $now, string $iterationUuid): bool
    {
        // Проверка наличия последнего рекуррентного платежа
        $latestPayment = $user->latestRecurrentPayment;
        if ($latestPayment === null) {
            return true;
        }

        // Проверка, был ли последний платеж неуспешным
        $isFailed = ((int) $latestPayment->status) === Payment::STATUS_DECLINED;
        if (!$isFailed) {
            return false;
        }

        // Определение периода задержки в зависимости от кода ошибки
        $isDelayedError = in_array($latestPayment->error_code, $this->errorCodesDelay);
        $delayDays = $isDelayedError ? $this->delayAfterUnsuccessfulPaymentsWithDelayDays : $this->delayAfterUnsuccessfulPaymentsDays;

        // Проверка, прошло ли достаточно времени с момента последнего неуспешного платежа
        $allowedRetryDate = $now->subDays($delayDays);
        $isRecent = $latestPayment->created_at->gt($allowedRetryDate);

        // Логирование и возврат false, если платеж был недавно
        if ($isRecent) {
            Log::error('У пользователя есть недавние неуспешные платежи.', [
                'user_id' => $user->id,
                'last_recurrent_payment_id' => $latestPayment->id,
                'recurrent_payment_consequent_error_count' => $user->recurrent_payment_consequent_error_count,
            ]);
            return false;
        }

        return true;
    }
}
