<?php


namespace App\Services\ReportService;


use App\Builders\ConversionBuilder;
use App\Builders\SmsUserBuilder;
use App\Models\Conversion;
use App\Models\SmsUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class SmsReportService
{
    /**
     * @var SmsReportStatisticsDataHolder
     */
    protected SmsReportStatisticsDataHolder $dataHolder;

    /**
     * BannerReportService constructor.
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @param string|null $smsId
     * @param int $groupType
     * @param User $user
     */
    public function __construct(protected Carbon  $dateFrom,
                                protected Carbon  $dateTo,
                                protected ?string $smsId,
                                protected int     $groupType,
                                protected User    $user)
    {
        // Nothing
    }

    /**
     * @return array
     */
    public function getSmsStatistics(): array
    {
        $this->dataHolder = new SmsReportStatisticsDataHolder();

        $this->loadSmsConversions();
        $this->loadSmsCost();

        return collect($this->dataHolder->getData())
            ->map(function (array $conversionData) {
                if (!isset($conversionData['conversions'])) {
                    $conversionData['conversions'] = 0;
                }

                if (!isset($conversionData['cost'])) {
                    $conversionData['cost'] = 0;
                }

                $conversionData['total'] = $conversionData['conversions'] - $conversionData['cost'];

                return $conversionData;
            })
            ->toArray();
    }

    /**
     * @return void
     * @todo Логика частично пересекается с RevenueReportStatisticsService
     */
    protected function loadSmsConversions(): void
    {
        Conversion::query()
            ->selectRaw("sum(IFNULL(conversions.api_payout, 0)) as conversions")
            ->whereApiStatusApproved()
            ->whereIn('type', [Conversion::TYPE_SMS])
            ->where('conversions.api_created_at', '>=', $this->dateFrom)
            ->where('conversions.api_created_at', '<=', $this->dateTo)
            ->when(is_numeric($this->smsId), function (ConversionBuilder $query) {
                $query->where('conversions.sms_id', $this->smsId);
            })
            ->when($this->smsId === 'all', function (ConversionBuilder $query) {
                $query->selectRaw('conversions.sms_id')
                    ->groupBy(['conversions.sms_id']);
            })
            ->when(
                $this->groupType === 1,
                function (Builder $query) {
                    $query
                        ->selectRaw('DATE_FORMAT(conversions.api_created_at, \'%d.%m.%Y\') as date')
                        ->groupByRaw('date');
                }
            )
            ->when(
                $this->groupType === 2,
                function (Builder $query) {
                    $query->selectRaw('DATE_FORMAT(conversions.api_created_at, \'%m.%Y\') as date')
                        ->groupByRaw('date');
                })
            ->when(
                $this->groupType === 3,
                function (Builder $query) {
                    $query->selectRaw('DATE_FORMAT(conversions.api_created_at, \'%Y\') as date')
                        ->groupByRaw('date');
                })
            ->orderBy('date')
            ->each(function (Conversion $conversion) {
                $this->dataHolder->setData(
                    $conversion->getAttribute('date'),
                    $conversion->getAttribute('sms_id'),
                    [
                        'conversions' => $conversion->getAttribute('conversions'),
                    ],
                );
            });
    }

    /**
     * @return void
     * @todo Логика частично пересекается с RevenueReportStatisticsService
     */
    protected function loadSmsCost(): void
    {
        SmsUser::query()
            ->selectRaw('sum(sms_user.cost) as cost')
            ->when(is_numeric($this->smsId), function (SmsUserBuilder $query) {
                $query->where('sms_user.sms_id', $this->smsId);
            })
            ->when($this->smsId === 'all', function (SmsUserBuilder $query) {
                $query->selectRaw('sms_user.sms_id')
                    ->groupBy(['sms_user.sms_id']);
            })
            ->when(
                $this->groupType === 1,
                function (Builder $query) {
                    $query
                        ->selectRaw('DATE_FORMAT(sms_user.created_at, \'%d.%m.%Y\') as date')
                        ->groupByRaw('date');
                }
            )
            ->when(
                $this->groupType === 2,
                function (Builder $query) {
                    $query->selectRaw('DATE_FORMAT(sms_user.created_at, \'%m.%Y\') as date')
                        ->groupByRaw('date');
                })
            ->when(
                $this->groupType === 3,
                function (Builder $query) {
                    $query->selectRaw('DATE_FORMAT(sms_user.created_at, \'%Y\') as date')
                        ->groupByRaw('date');
                })
            ->whereNotIn('status', [SmsUser::STATUS_SENDING_FAILED])
            ->where('sms_user.created_at', '>=', $this->dateFrom)
            ->where('sms_user.created_at', '<=', $this->dateTo)
            ->orderBy('date')
            ->each(function (SmsUser $smsUser) {
                $this->dataHolder->setData(
                    $smsUser->getAttribute('date'),
                    $smsUser->getAttribute('sms_id'),
                    [
                        'cost' => $smsUser->getAttribute('cost'),
                    ],
                );
            });
    }
}