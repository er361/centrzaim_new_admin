<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Postback;
use App\Models\SmsUser;
use App\Models\User;
use App\Repositories\PaymentRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use stdClass;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param PaymentRepository $paymentRepository
     * @return Application|Factory|View
     */
    public function index(PaymentRepository $paymentRepository): View|Factory|Application
    {
        // @todo Вынести в отдельные классы

        if (!Gate::allows('report_access')) {
            return view('admin.home');
        }

        $defaultPaymentsQuery = Payment::query()->whereTypeDefault();
        $defaultSuccessPaymentsQuery = Payment::query()->whereCardAdded();

        $paymentPlans = User::query()
            ->select([
                'payment_plan',
            ])
            ->distinct()
            ->pluck('payment_plan');

        $now = CarbonImmutable::now();
        $labels = [
            'Последний час' => $now->subHour(),
            'Последние 6 часов' => $now->subHours(6),
            'Последние 24 часа' => $now->subDay(),
        ];

        $payments = [];
        $sms = [];
        $postbacks = [];

        foreach ($labels as $label => $createdAt) {
            $payments[$label] = [];

            foreach ($paymentPlans as $paymentPlan) {
                $payments[$label][$paymentPlan] = [
                    'default_payments' => $defaultPaymentsQuery->clone()->wherePaymentPlan($paymentPlan)->whereCreatedAtAfter($createdAt)->count(),
                    'default_success_payments' => $defaultSuccessPaymentsQuery->clone()->wherePaymentPlan($paymentPlan)->whereCreatedAtAfter($createdAt)->count(),
                    'recurrent_payments' => 0,
                    'recurrent_success_payments' => 0,
                    'recurrent_success_payments_sum' => 0,
                    'recurrent_payments_distribution' => [],
                ];
            }

            $recurrentPaymentsData = $paymentRepository->getRecurrentPaymentsStatistics($createdAt)->get();

            /** @var stdClass $recurrentPaymentRecord */
            foreach ($recurrentPaymentsData as $recurrentPaymentRecord) {
                $paymentPlan = $recurrentPaymentRecord->payment_plan;
                $payments[$label][$paymentPlan]['recurrent_payments'] = $recurrentPaymentRecord->recurrent_payments;
                $payments[$label][$paymentPlan]['recurrent_success_payments'] = $recurrentPaymentRecord->recurrent_success_payments;
                $payments[$label][$paymentPlan]['recurrent_success_payments_sum'] = $recurrentPaymentRecord->recurrent_success_payments_sum;
            }

            $recurrentPaymentsByIteration = $paymentRepository->getRecurrentPaymentsStatisticsByIterations($createdAt)->get();

            /** @var stdClass $recurrentPaymentIteration */
            foreach ($recurrentPaymentsByIteration as $recurrentPaymentIteration) {
                $paymentPlan = $recurrentPaymentIteration->payment_plan;
                $iterationNumber = $recurrentPaymentIteration->iteration_number;
                $iterationKey = "$label.$paymentPlan.recurrent_payments_distribution.$iterationNumber";
                $paymentKey = "$iterationKey.$recurrentPaymentIteration->payment_number";
                Arr::set($payments, $paymentKey, [
                    'total' => $recurrentPaymentIteration->total,
                    'amount' => $recurrentPaymentIteration->amount,
                ]);
            }

            $sms[$label] = SmsUser::query()->where('created_at', '>=', $createdAt)->count();
            $postbacks[$label] = [
                'created' => Postback::query()->where('created_at', '>=', $createdAt)->count(),
                'sent' => Postback::query()->where('created_at', '>=', $createdAt)->whereNotNull('sent_at')->count(),
                'unsuccessful' => Postback::query()->where('created_at', '>=', $createdAt)->whereNull('sent_at')->where('created_at', '<=', $now->subMinute())->count(),
            ];
        }

        $paymentsReportEndAt = Carbon::now();
        $paymentsReportStartFrom = $paymentsReportEndAt->clone()->subDays(21);
        $period = CarbonPeriod::create($paymentsReportStartFrom, $paymentsReportEndAt);
        $paymentGraphData = Payment::query()
            ->whereTypeRecurrent()
            ->whereCreatedAtAfter($paymentsReportStartFrom)
            ->select([
                DB::raw('cast(created_at as date) AS created_date'),
                'status',
                DB::raw('count(*) as total')
            ])
            ->groupBy([
                'created_date',
                'status',
            ])
            ->get();

        $statuses = $paymentGraphData->pluck('status', 'status');
        $paymentsPerDay = [];

        foreach ($statuses as $status) {
            $paymentsPerDay[$status] = [];

            foreach ($period as $date) {
                $paymentsPerDay[$status][$date->toDateString()] = 0;
            }
        }

        /** @var Payment $paymentGraphItem */
        foreach ($paymentGraphData as $paymentGraphItem) {
            $key = $paymentGraphItem->status . '.' . $paymentGraphItem->getAttribute('created_date');
            Arr::set($paymentsPerDay, $key, $paymentGraphItem->getAttribute('total'));
        }

        $paymentsPerDayDates = array_keys(Arr::first($paymentsPerDay) ?? []);

        return view('admin.home', compact('payments', 'sms', 'labels', 'postbacks', 'paymentsPerDay', 'paymentsPerDayDates'));
    }
}
