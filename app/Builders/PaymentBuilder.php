<?php

namespace App\Builders;

use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @method PaymentBuilder filter($query, array $input = [], $filter = null)
 * @method Collection<int, Payment> get($columns = ['*'])
 *
 * @template TModelClass of Model&Payment
 *
 * @extends BaseBuilder<Payment>
 */
class PaymentBuilder extends BaseBuilder
{
    /**
     * Фильтр по привязкам карты.
     * @return PaymentBuilder
     */
    public function whereCardAdded(): PaymentBuilder
    {
        return $this
            ->whereServiceImpaya()
            ->whereStatusInSuccess()
            ->whereTypeDefault()
            ->whereNotNull('rebill_id');
    }

    /**
     * Фильтр по платежам, в рамках которых была критическая ошибка, не дающая дальше списывать деньги.
     * @return PaymentBuilder
     */
    public function whereCriticalError(): PaymentBuilder
    {
        return $this
            ->whereServiceImpaya()
            ->whereIn('error_code', config('services.impaya.error_codes.disable'));
    }

    /**
     * Поиск по платежам через Impaya.
     * @return PaymentBuilder
     * @todo Изменить логику, чтобы не хардкодить Impaya (учитывать текущий сервис?)
     */
    public function whereServiceImpaya(): PaymentBuilder
    {
        return $this->where('service', Payment::SERVICE_IMPAYA);
    }

    /**
     * Фильтр по рекуррентным платежам.
     *
     * @return PaymentBuilder
     */
    public function whereTypeRecurrent(): PaymentBuilder
    {
        return $this->where('type', Payment::TYPE_RECURRENT);
    }

    /**
     * Фильтр по обычным платежам.
     *
     * @return PaymentBuilder
     */
    public function whereTypeDefault(): PaymentBuilder
    {
        return $this->where('type', Payment::TYPE_DEFAULT);
    }

    /**
     * Фильтр по неуспешным платежам.
     *
     * @return PaymentBuilder
     */
    public function whereStatusFailed(): PaymentBuilder
    {
        return $this->where('status', Payment::STATUS_DECLINED);
    }

    /**
     * Фильтр по успешным платежам.
     *
     * @return PaymentBuilder
     */
    public function whereStatusPayed(): PaymentBuilder
    {
        return $this->where('status', Payment::STATUS_PAYED);
    }

    /**
     * Фильтр по платежам со всеми успешными статусами.
     *
     * @return PaymentBuilder
     */
    public function whereStatusInSuccess(): PaymentBuilder
    {
        return $this->whereIn('status', [
            Payment::STATUS_PAYED,
            Payment::STATUS_CARD_ADDED,
        ]);
    }

    /**
     * Фильтр по платежам, созданным после.
     *
     * @param CarbonInterface $from
     * @return PaymentBuilder
     */
    public function whereCreatedAtAfter(CarbonInterface $from): PaymentBuilder
    {
        return $this->where('payments.created_at', '>=', $from);
    }

    /**
     * Фильтр по типу платежа.
     * @param int $paymentPlan
     * @return PaymentBuilder
     */
    public function wherePaymentPlan(int $paymentPlan): PaymentBuilder
    {
        return $this->whereHas('user', function (UserBuilder $query) use ($paymentPlan) {
            $query->wherePaymentPlan($paymentPlan);
        });
    }

    /**
     * Фильтр по номеру платежа.
     * @param int $paymentNumber
     * @return PaymentBuilder
     */
    public function wherePaymentNumber(int $paymentNumber): PaymentBuilder
    {
        return $this->where('payment_number', $paymentNumber);
    }

    /**
     * Фильтр по номеру итерации.
     * @param int $paymentNumber
     * @return PaymentBuilder
     */
    public function whereIterationNumber(int $paymentNumber): PaymentBuilder
    {
        return $this->where('iteration_number', $paymentNumber);
    }

    /**
     * @param User $user
     * @return PaymentBuilder
     */
    public function forUser(User $user): PaymentBuilder
    {
        if ($user->role_id === Role::ID_ADMIN || $user->isSuperAdmin()) {
            return $this;
        }

        if ($user->role_id !== Role::ID_TRAFFIC_SOURCE) {
            return $this->whereRaw('0 = 1');
        }

        return $this->whereHas('user', function (UserBuilder $query) use ($user) {
            $query->whereHas('webmaster', function (WebmasterBuilder $query) use ($user) {
                $query->forUser($user);
            });
        });
    }

    /**
     * @return PaymentBuilder
     */
    public function whereErrorCodeRequireDelay(): PaymentBuilder
    {
        return $this
            ->whereServiceImpaya()
            ->whereIn('error_code', config('services.impaya.error_codes.delay'));
    }
}