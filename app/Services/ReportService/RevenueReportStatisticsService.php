<?php


namespace App\Services\ReportService;


use App\Models\Action;
use App\Models\BannerStatistic;
use App\Models\Conversion;
use App\Models\Payment;
use App\Models\Postback;
use App\Models\SmsUser;
use App\Models\Statistic;
use App\Models\User;
use App\Models\Webmaster;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RevenueReportStatisticsService
{
    /**
     * @var CarbonInterface
     */
    protected CarbonInterface $dateFrom;

    /**
     * @var CarbonInterface
     */
    protected CarbonInterface $dateTo;

    /**
     * @var RevenueReportStatisticsDataHolder
     */
    protected RevenueReportStatisticsDataHolder $dataHolder;

    /**
     * RevenueReportStatisticsService constructor.
     * @param CarbonInterface|null $dateFrom
     * @param CarbonInterface|null $dateTo
     */
    public function __construct(?CarbonInterface $dateFrom = null, ?CarbonInterface $dateTo = null)
    {
        $this->dateFrom = $dateFrom ?? Carbon::yesterday();
        $this->dateTo = $dateTo ?? Carbon::now();
    }

    public function update(): void
    {
        $this->dataHolder = new RevenueReportStatisticsDataHolder();

        $this->loadActions();
        $this->loadUsers();
        $this->loadConversions();
        $this->loadSmsCost();
        $this->loadPayments();
        $this->loadLtvSum();
        $this->loadBanners();
        $this->loadPostbacks();

        $dataToInsert = $this->dataHolder->getData();
        $tableKeys = [
            'date',
            'source_id',
            'webmaster_id',
        ];

        /**
         * Ключ - название поля, значение - как учитываем его значение.
         * -1 - затраты, 0 - не учитываем, 1 - прибыль.
         */
        $totalFields = [
            'actions_count' => 0,
            'users_count' => 0,
            'active_users_count' => 0,
            'card_added_users_count' => 0,
            'dashboard_conversions' => 1,
            'sms_conversions' => 1,
            'sms_cost_sum' => -1,
            'payments_sum' => 1,
            'ltv_sum' => 1,
            'banners_sum' => 1,
            'postback_count' => 0,
            'postback_cost_sum' => -1,
        ];

        $webmasters = Webmaster::query()
            ->whereIn('id', array_column($dataToInsert, 'webmaster_id'))
            ->get()
            ->keyBy('id');

        $version = Str::uuid()->toString();

        foreach ($dataToInsert as $record) {
            $record['total'] = 0;

            foreach ($record as $fieldName => $value) {
                if (Arr::has($totalFields, $fieldName)) {
                    $record['total'] += $value * $totalFields[$fieldName];
                }
            }

            $record['webmaster_income_coefficient'] = $webmasters->get((int)$record['webmaster_id'])?->income_percent;
            $record['version'] = $version;

            // Если в выборке изменился день, и теперь нет строчки за выбранную дату, мы ее не занулим, фиксим
            $missedKeys = array_diff(
                array_keys($totalFields),
                array_keys($record)
            );

            foreach ($missedKeys as $missedKey) {
                $record[$missedKey] = 0;
            }

            Statistic::query()
                ->updateOrCreate(
                    Arr::only($record, $tableKeys),
                    Arr::except($record, $tableKeys)
                );
        }

        Statistic::query()
            ->where('date', '>=', $this->dateFrom)
            ->where('date', '<=', $this->dateTo)
            ->where('version', '!=', $version)
            ->delete();
    }

    protected function loadActions(): void
    {
        Action::query()
            ->selectRaw('count(actions.id) as actions_count')
            ->selectRaw("DATE_FORMAT(actions.created_at, '%Y-%m-%d') as date")
            ->where('actions.created_at', '>=', $this->dateFrom)
            ->where('actions.created_at', '<=', $this->dateTo)
            ->leftJoin('webmasters', 'webmasters.id', '=', 'actions.webmaster_id')
            ->selectRaw('webmasters.source_id')
            ->selectRaw('webmasters.id as webmaster_id')
            ->groupBy(['webmasters.source_id', 'webmasters.id', 'date'])
            ->orderBy('webmasters.source_id')
            ->orderBy('webmasters.id')
            ->orderBy('date')
            ->each(function (Action $action) {
                $this->dataHolder->setData(
                    $action->getAttribute('date'),
                    $action->getAttribute('source_id'),
                    $action->getAttribute('webmaster_id'),
                    [
                        'actions_count' => $action->getAttribute('actions_count'),
                    ]
                );
            });
    }

    protected function loadUsers(): void
    {
        User::query()
            ->selectRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') as date")
            ->selectRaw('count(users.id) as users_count')
            ->selectRaw('SUM(case when users.is_active = 1 then 1 else 0 end) as active_users_count')
            ->whereNotNull('webmaster_id')
            ->where('users.created_at', '>=', $this->dateFrom)
            ->where('users.created_at', '<=', $this->dateTo)
            ->leftJoin('webmasters', 'webmasters.id', '=', 'users.webmaster_id')
            ->selectRaw('webmasters.source_id')
            ->selectRaw('webmasters.id as webmaster_id')
            ->groupBy(['webmasters.source_id', 'webmasters.id', 'date'])
            ->orderBy('webmasters.source_id')
            ->orderBy('webmasters.id')
            ->orderBy('date')
            ->each(function (User $user) {
                $this->dataHolder->setData(
                    $user->getAttribute('date'),
                    $user->getAttribute('source_id'),
                    $user->getAttribute('webmaster_id'),
                    [
                        'users_count' => $user->getAttribute('users_count'),
                        'active_users_count' => $user->getAttribute('active_users_count'),
                    ]
                );
            });
    }

    protected function loadConversions(): void
    {
        Conversion::query()
            ->selectRaw("DATE_FORMAT(conversions.api_created_at, '%Y-%m-%d') as date")
            ->selectRaw("IFNULL(SUM(case when (conversions.type = ? or conversions.type = ?) then conversions.api_payout else 0 end), 0) as dashboard_conversions", [Conversion::TYPE_DASHBOARD, Conversion::TYPE_PUBLIC_DASHBOARD])
            ->selectRaw("IFNULL(SUM(case when conversions.type = ? then conversions.api_payout else 0 end), 0) as sms_conversions", [Conversion::TYPE_SMS])
            ->whereApiStatusApproved()
            ->where('conversions.api_created_at', '>=', $this->dateFrom)
            ->where('conversions.api_created_at', '<=', $this->dateTo)
            ->leftJoin('webmasters', 'webmasters.id', '=', 'conversions.webmaster_id')
            ->selectRaw('webmasters.source_id')
            ->selectRaw('webmasters.id as webmaster_id')
            ->groupBy(['webmasters.source_id', 'webmasters.id', 'date'])
            ->orderBy('webmasters.source_id')
            ->orderBy('webmasters.id')
            ->orderBy('date')
            ->each(function (Conversion $conversion) {
                $this->dataHolder->setData(
                    $conversion->getAttribute('date'),
                    $conversion->getAttribute('source_id'),
                    $conversion->getAttribute('webmaster_id'),
                    [
                        'dashboard_conversions' => $conversion->getAttribute('dashboard_conversions'),
                        'sms_conversions' => $conversion->getAttribute('sms_conversions'),
                       ]
                );
            });
    }

    protected function loadSmsCost(): void
    {
        SmsUser::query()
            ->selectRaw('sum(sms_user.cost) as sms_cost_sum')
            ->selectRaw("DATE_FORMAT(sms_user.created_at, '%Y-%m-%d') as date")
            ->whereNotIn('status', [SmsUser::STATUS_SENDING_FAILED])
            ->where('sms_user.created_at', '>=', $this->dateFrom)
            ->where('sms_user.created_at', '<=', $this->dateTo)
            ->leftJoin('users', 'users.id', '=', 'sms_user.user_id')
            ->leftJoin('webmasters', 'webmasters.id', '=', 'users.webmaster_id')
            ->selectRaw('webmasters.source_id')
            ->selectRaw('webmasters.id as webmaster_id')
            ->groupBy(['webmasters.source_id', 'webmasters.id', 'date'])
            ->orderBy('webmasters.source_id')
            ->orderBy('webmasters.id')
            ->orderBy('date')
            ->each(function (SmsUser $smsUser) {
                $this->dataHolder->setData(
                    $smsUser->getAttribute('date'),
                    $smsUser->getAttribute('source_id'),
                    $smsUser->getAttribute('webmaster_id'),
                    [
                        'sms_cost_sum' => $smsUser->getAttribute('sms_cost_sum'),
                    ]
                );
            });
    }

    protected function loadPayments(): void
    {
        $basePaymentsQuery = Payment::query()
            ->selectRaw("DATE_FORMAT(payments.created_at, '%Y-%m-%d') as date")
            ->where('payments.created_at', '>=', $this->dateFrom)
            ->where('payments.created_at', '<=', $this->dateTo)
            ->leftJoin('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('webmasters', 'webmasters.id', '=', 'users.webmaster_id')
            ->selectRaw('webmasters.source_id')
            ->selectRaw('webmasters.id as webmaster_id')
            ->groupBy(['webmasters.source_id', 'webmasters.id', 'date'])
            ->orderBy('webmasters.source_id')
            ->orderBy('webmasters.id')
            ->orderBy('date');

        $basePaymentsQuery
            ->clone()
            ->selectRaw('SUM(CASE WHEN status = ? THEN payments.amount - IFNULL(payments.commission, 0) ELSE -IFNULL(payments.commission, 0) END) as payments_sum', [Payment::STATUS_PAYED])
            ->each(function (Payment $payment) {
                $this->dataHolder->setData(
                    $payment->getAttribute('date'),
                    $payment->getAttribute('source_id'),
                    $payment->getAttribute('webmaster_id'),
                    [
                        'payments_sum' => $payment->getAttribute('payments_sum'),
                    ]
                );
            });

        $basePaymentsQuery
            ->clone()
            ->whereTypeDefault()
            ->whereCardAdded()
            ->selectRaw('count(distinct user_id) as card_added_users_count')
            ->each(function (Payment $payment) {
                $this->dataHolder->setData(
                    $payment->getAttribute('date'),
                    $payment->getAttribute('source_id'),
                    $payment->getAttribute('webmaster_id'),
                    [
                        'card_added_users_count' => $payment->getAttribute('card_added_users_count'),
                    ]
                );
            });
    }
    
    /**
     * Calculate LTV sum - payments sum from users registered on the same date
     */
    protected function loadLtvSum(): void
    {
        // For each date in the range, find users registered on that date and count their payments
        foreach (new \DatePeriod($this->dateFrom, new \DateInterval('P1D'), $this->dateTo->addDay()) as $date) {
            $formattedDate = $date->format('Y-m-d');
            
            // Get all payments from users who were registered on this specific date
            $paymentBuilder = Payment::query()
                ->selectRaw('SUM(CASE WHEN status = ? THEN payments.amount - IFNULL(payments.commission, 0) ELSE -IFNULL(payments.commission, 0) END) as ltv_sum', [Payment::STATUS_PAYED])
                ->selectRaw('webmasters.source_id')
                ->selectRaw('webmasters.id as webmaster_id')
                ->where('payments.created_at', '>=', $this->dateFrom)
                ->where('payments.created_at', '<=', $this->dateTo)
                ->leftJoin('users', 'users.id', '=', 'payments.user_id')
                ->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') = ?", [$formattedDate])
                ->leftJoin('webmasters', 'webmasters.id', '=', 'users.webmaster_id')
                ->groupBy(['webmasters.source_id', 'webmasters.id'])
                ->orderBy('webmasters.source_id')
                ->orderBy('webmasters.id');
            $paymentBuilder
                ->each(function (Payment $payment) use ($formattedDate) {
                    $this->dataHolder->setData(
                        $formattedDate,
                        $payment->getAttribute('source_id'),
                        $payment->getAttribute('webmaster_id'),
                        [
                            'ltv_sum' => $payment->getAttribute('ltv_sum') ?? 0,
                        ]
                    );
                });
        }
    }

    protected function loadBanners(): void
    {
        BannerStatistic::query()
            ->selectRaw("DATE_FORMAT(banner_statistics.api_date, '%Y-%m-%d') as date")
            ->selectRaw('SUM(revenue) as banners_sum')
            ->where('banner_statistics.api_date', '>=', $this->dateFrom)
            ->where('banner_statistics.api_date', '<=', $this->dateTo)
            ->leftJoin('webmasters', 'webmasters.id', '=', 'banner_statistics.webmaster_id')
            ->selectRaw('webmasters.source_id')
            ->selectRaw('webmasters.id as webmaster_id')
            ->groupBy(['webmasters.source_id', 'webmasters.id', 'date'])
            ->orderBy('webmasters.source_id')
            ->orderBy('webmasters.id')
            ->orderBy('date')
            ->each(function (BannerStatistic $bannerStatistic) {
                $this->dataHolder->setData(
                    $bannerStatistic->getAttribute('date'),
                    $bannerStatistic->getAttribute('source_id'),
                    $bannerStatistic->getAttribute('webmaster_id'),
                    [
                        'banners_sum' => $bannerStatistic->getAttribute('banners_sum'),
                    ]
                );
            });
    }

    public function loadPostbacks(): void
    {
        Postback::query()
            ->selectRaw("DATE_FORMAT(postbacks.created_at, '%Y-%m-%d') as date")
            ->selectRaw('IFNULL(SUM(postbacks.cost), 0) as postback_cost_sum')
            ->selectRaw('IFNULL(COUNT(postbacks.id), 0) as postback_count')
            ->where('postbacks.created_at', '>=', $this->dateFrom)
            ->where('postbacks.created_at', '<=', $this->dateTo)
            ->leftJoin('users', 'users.id', '=', 'postbacks.user_id')
            ->leftJoin('webmasters', 'webmasters.id', '=', 'users.webmaster_id')
            ->selectRaw('webmasters.source_id')
            ->selectRaw('webmasters.id as webmaster_id')
            ->groupBy(['webmasters.source_id', 'webmasters.id', 'date'])
            ->orderBy('webmasters.source_id')
            ->orderBy('webmasters.id')
            ->orderBy('date')
            ->each(function (Postback $postback) {
                $this->dataHolder->setData(
                    $postback->getAttribute('date'),
                    $postback->getAttribute('source_id'),
                    $postback->getAttribute('webmaster_id'),
                    [
                        'postback_cost_sum' => $postback->getAttribute('postback_cost_sum'),
                        'postback_count' => $postback->getAttribute('postback_count'),
                    ]
                );
            });
    }
}