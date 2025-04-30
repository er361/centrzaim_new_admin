<?php

namespace App\Repositories;

use App\Builders\PaymentBuilder;
use App\Models\Payment;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class PaymentRepository
{
    /**
     * @param CarbonInterface $createdAtAfter
     * @return PaymentBuilder<Payment>
     */
    public function getRecurrentPaymentsStatistics(CarbonInterface $createdAtAfter): PaymentBuilder
    {
        return Payment::query()
            ->select([
                'users.payment_plan',
                DB::raw('count(payments.id) as recurrent_payments'),
            ])
            ->selectRaw('sum(IF(payments.status = ?, 1, 0)) as recurrent_success_payments', [Payment::STATUS_PAYED])
            ->selectRaw('sum(IF(payments.status = ?, payments.amount, 0)) as recurrent_success_payments_sum', [Payment::STATUS_PAYED])
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->whereTypeRecurrent()
            ->whereCreatedAtAfter($createdAtAfter)
            ->groupBy([
                'users.payment_plan'
            ])
            ->orderBy('users.payment_plan');
    }

    /**
     * @param CarbonInterface $createdAtAfter
     * @return PaymentBuilder
     */
    public function getRecurrentPaymentsStatisticsByIterations(CarbonInterface $createdAtAfter): PaymentBuilder
    {
        return Payment::query()
            ->whereTypeRecurrent()
            ->select([
                'users.payment_plan',
                DB::raw('count(payments.id) as total'),
                DB::raw('sum(payments.amount) as amount'),
                'payments.iteration_number',
                'payments.payment_number',
            ])
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->whereCreatedAtAfter($createdAtAfter)
            ->whereStatusPayed()
            ->whereNotNull([
                'payments.iteration_number',
                'payments.payment_number',
            ])
            ->groupBy([
                'users.payment_plan',
                'payments.iteration_number',
                'payments.payment_number',
            ])
            ->orderBy('users.payment_plan')
            ->orderBy('payments.iteration_number')
            ->orderBy('payments.payment_number');
    }
}