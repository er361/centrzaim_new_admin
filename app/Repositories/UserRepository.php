<?php

namespace App\Repositories;

use App\Builders\PaymentBuilder;
use App\Builders\UserBuilder;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function getUsersForRecurrentCharge(): UserBuilder
    {
        // 1. Основной запрос к пользователям с базовыми условиями
        $query = User::query()
            ->with(['latestRecurrentPayment'])
            ->orderBy('id')
            ->whereEnabled();

        // 2. Проверка привязки карты
        $cardAttachedCondition = fn(PaymentBuilder $q) => $q->whereCardAdded();
        $query->whereHas('payments', $cardAttachedCondition);

        // 3. Проверка отсутствия критичных ошибок
        $noCriticalErrorsCondition = fn(PaymentBuilder $q) => $q->whereTypeRecurrent()->whereCriticalError();
        $query->whereDoesntHave('payments', $noCriticalErrorsCondition);


        return $query;
    }

    public function getUsersWithSuccessfulPayments(): UserBuilder
    {
        $query = $this->getUsersForRecurrentCharge();

        $monthlyIntervalInMinutes = config('payments_miazaim.monthly.interval_in_minutes');

        // Условие для исключения пользователей с неуспешными платежами
        $failedPaymentCondition = fn(PaymentBuilder $q) => $q->whereTypeRecurrent()
            ->whereIn('subtype', [Payment::SUBTYPE_WEEKLY, Payment::SUBTYPE_RETRY_AFTER_FAILED_MONTHLY])
            ->orWhere(fn(PaymentBuilder $q) => $q->whereStatusFailed());

        $query->whereDoesntHave('payments', $failedPaymentCondition);

        // Условие для пользователей с успешным рекуррентным платежом
        $time = Carbon::now()->subMinutes($monthlyIntervalInMinutes);


        $successfulPaymentCondition = fn(PaymentBuilder $q) => $q->whereTypeRecurrent()
            ->whereStatusPayed()
            ->where('created_at','>=', $time);

        // Добавить условие для пользователей без рекуррентных платежей
        $query->where(function (Builder $subQuery) use ($successfulPaymentCondition) {
            $subQuery
                ->whereDoesntHave('payments', fn(PaymentBuilder $q) => $q->whereTypeRecurrent())
                ->orWhereDoesntHave('payments', $successfulPaymentCondition);
        });

        return $query;
    }

    public function getUsersWithErrors(): UserBuilder
    {
        $query = $this->getUsersForRecurrentCharge();

        // Фильтрация пользователей, у которых есть хотя бы один неуспешный платеж с суммой месячного платежа
        $errorCondition = fn(PaymentBuilder $q) => $q->whereTypeRecurrent()
            ->whereStatusFailed()
            ->whereSubtype(Payment::SUBTYPE_MONTHLY);

        $query->whereHas('payments', $errorCondition);

        return $query;
    }

}
