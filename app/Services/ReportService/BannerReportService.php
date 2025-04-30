<?php


namespace App\Services\ReportService;


use App\Models\BannerStatistic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class BannerReportService
{
    /**
     * BannerReportService constructor.
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @param string|null $position
     * @param string|null $bannerId
     * @param null|string $sourceId
     * @param null|string $webmasterId
     * @param int $groupType
     * @param User $user
     */
    public function __construct(protected Carbon  $dateFrom,
                                protected Carbon  $dateTo,
                                protected ?string $position,
                                protected ?string $bannerId,
                                protected ?string $sourceId,
                                protected ?string $webmasterId,
                                protected int     $groupType,
                                protected User    $user)
    {
        // Nothing
    }

    /**
     * @return array
     */
    public function getBanners(): array
    {
        return BannerStatistic::query()
            ->selectRaw('SUM(impressions) as impressions_sum')
            ->selectRaw('SUM(clicks) as clicks_sum')
            ->selectRaw('SUM(clicks)/SUM(impressions) as ctr_avg')
            ->selectRaw('SUM(revenue) as revenue_sum')
            ->selectRaw('SUM(revenue)/SUM(impressions)*1000 as e_cpm_avg')
            ->forUser($this->user)
            ->when($this->position !== null && $this->position !== 'all', function (Builder $query) {
                $query->where('banners.position', $this->position);
            })
            ->when(is_numeric($this->bannerId), function (Builder $query) {
                $query->where('banner_statistics.banner_id', $this->bannerId);
            })
            ->when(is_numeric($this->sourceId), function (Builder $query) {
                $query->where('banner_statistics.source_id', $this->sourceId);
            })
            ->when(is_numeric($this->webmasterId), function (Builder $query) {
                $query->where('webmaster_id', $this->webmasterId);
            })
            ->leftJoin('banners', 'banners.id', '=', 'banner_statistics.banner_id')
            ->leftJoin('webmasters', 'webmasters.id', '=', 'banner_statistics.webmaster_id')
            ->when($this->position === 'all' || $this->bannerId === 'all' || $this->sourceId === 'all' || $this->webmasterId === 'all', function (Builder $query) {
                if ($this->position === 'all') {
                    $query
                        ->selectRaw('banners.position')
                        ->groupBy(['banners.position']);
                }

                if ($this->bannerId === 'all') {
                    $query
                        ->selectRaw('banner_statistics.banner_id')
                        ->groupBy(['banner_statistics.banner_id']);
                }

                if ($this->webmasterId === 'all') {
                    $query
                        ->selectRaw('webmasters.source_id')
                        ->selectRaw('webmasters.api_id')
                        ->groupBy(['webmasters.source_id', 'api_id']);
                } else {
                    $query
                        ->selectRaw('banner_statistics.source_id')
                        ->groupBy(['banner_statistics.source_id']);
                }
            })
            ->where('banner_statistics.api_date', '>=', $this->dateFrom)
            ->where('banner_statistics.api_date', '<=', $this->dateTo)
            ->when(
                $this->groupType === 1,
                function (Builder $query) {
                    $query
                        ->selectRaw('DATE_FORMAT(banner_statistics.api_date, \'%d.%m.%Y\') as date')
                        ->groupByRaw('date');
                }
            )
            ->when(
                $this->groupType === 2,
                function (Builder $query) {
                    $query->selectRaw('DATE_FORMAT(banner_statistics.api_date, \'%m.%Y\') as date')
                        ->groupByRaw('date');
                })
            ->when(
                $this->groupType === 3,
                function (Builder $query) {
                    $query->selectRaw('DATE_FORMAT(banner_statistics.api_date, \'%Y\') as date')
                        ->groupByRaw('date');
                })
            ->get()
            ->toArray();
    }
}