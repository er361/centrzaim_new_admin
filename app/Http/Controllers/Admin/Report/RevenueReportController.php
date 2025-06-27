<?php


namespace App\Http\Controllers\Admin\Report;


use App\Builders\StatisticBuilder;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Source;
use App\Models\Statistic;
use App\Models\StatisticsActionsDetailed;
use App\Models\User;
use App\Models\Webmaster;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RevenueReportController extends Controller
{
    /**
     * @param Request $request
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        if (!Gate::allows('revenue_report_access')) {
            abort(401);
        }

        /** @var User $user */
        $user = Auth::user();
        $shouldShowSalary = $user->role_id === Role::ID_TRAFFIC_SOURCE;

        if ($request->has('date_from') && $request->has('date_to')) {
            $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
            $dateTo = Carbon::parse($request->input('date_to'))->endOfDay();

            $sourceIds = empty($request->input('source_id')) ? null : (array)$request->input('source_id');
            $webmasterIds = empty($request->input('webmaster_id')) ? null : (array)$request->input('webmaster_id');
            $groupType = empty($request->input('group_type')) ? null : (int)$request->input('group_type');

            if (is_array($webmasterIds)) {
                foreach ($webmasterIds as $webmasterId) {
                    if (is_numeric($webmasterId)) {
                        $webmaster = Webmaster::query()->find($webmasterId);
                        $this->authorize('view', $webmaster);
                    }
                }
            }

            /** @var User $user */
            $user = Auth::user();

            $rows = Statistic::query()
                ->select([
                    DB::raw('sum(actions_count) as actions_count'),
                    DB::raw('sum(users_count) as users_count'),
                    DB::raw('sum(active_users_count) as active_users_count'),
                    DB::raw('sum(card_added_users_count) as card_added_users_count'),
                    DB::raw('sum(dashboard_conversions) as dashboard_conversions'),
                    DB::raw('sum(sms_conversions) as sms_conversions'),
                    DB::raw('sum(sms_cost_sum) as sms_cost_sum'),
                    DB::raw('sum(payments_sum) as payments_sum'),
                    DB::raw('sum(ltv_sum) as ltv_sum'),
                    DB::raw('sum(banners_sum) as banners_sum'),
                    DB::raw('sum(postback_count) as postback_count'),
                    DB::raw('sum(postback_cost_sum) as postback_cost_sum'),
                    DB::raw('sum(total) as total'),
                    DB::raw('sum(total * IFNULL(webmaster_income_coefficient, 0) / 100) as salary'),
                ])
                ->where('date', '>=', $dateFrom)
                ->where('date', '<=', $dateTo)
                ->forUser($user)
                ->when($sourceIds !== null && is_numeric(Arr::first($sourceIds)), function (StatisticBuilder $query) use ($sourceIds) {
                    $query
                        ->whereHas('webmaster', function (Builder $query) use ($sourceIds) {
                            $query->whereIn('source_id', $sourceIds);
                        })
                        ->groupBySourceId();
                })
                ->when($webmasterIds !== null && is_numeric(Arr::first($webmasterIds)), function (StatisticBuilder $query) use ($webmasterIds) {
                    $query
                        ->whereIn('webmaster_id', $webmasterIds)
                        ->groupByWebmasterApiId();
                })
                ->when(Arr::first($webmasterIds) === 'all' || Arr::first($sourceIds) === 'all', function (StatisticBuilder $query) use ($webmasterIds) {
                    if (Arr::first($webmasterIds) === 'all') {
                        $query->groupByWebmasterApiId();
                    } else {
                        $query->groupBySourceId();
                    }
                })
                ->when($groupType === 1, fn(StatisticBuilder $query) => $query->selectAndGroupByDay())
                ->when($groupType === 2, fn(StatisticBuilder $query) => $query->selectAndGroupByMonth())
                ->when($groupType === 3, fn(StatisticBuilder $query) => $query->selectAndGroupByYear())
                ->orderBy('formatted_date')
                ->get();
                
            // Получаем детализированную статистику по actions
            $detailedRows = StatisticsActionsDetailed::query()
                ->select([
                    'site_id',
                    'place_id', 
                    'banner_id',
                    'campaign_id',
                    'webmaster_id',
                    DB::raw('sum(actions_count) as actions_count'),
                ])
                ->with(['webmaster.source'])
                ->where('date', '>=', $dateFrom)
                ->where('date', '<=', $dateTo)
                ->when($sourceIds !== null && is_numeric(Arr::first($sourceIds)), function ($query) use ($sourceIds) {
                    $query->whereHas('webmaster', function (Builder $query) use ($sourceIds) {
                        $query->whereIn('source_id', $sourceIds);
                    });
                })
                ->when($webmasterIds !== null && is_numeric(Arr::first($webmasterIds)), function ($query) use ($webmasterIds) {
                    $query->whereIn('webmaster_id', $webmasterIds);
                })
                ->groupBy(['site_id', 'place_id', 'banner_id', 'campaign_id', 'webmaster_id'])
                ->orderBy('webmaster_id')
                ->orderBy('actions_count', 'desc')
                ->get();
        } else {
            $rows = collect();
            $detailedRows = collect();
        }

        $sources = Source::query()
            ->forUser($user)
            ->get()
            ->keyBy('id');
        $webmasters = Webmaster::query()
            ->forUser($user)
            ->with('source')
            ->get()
            ->keyBy('api_id');

        $sourceIds = (array)$request->input('source_id', []);
        $webmasterIds = (array)$request->input('webmaster_id', []);
        $shouldShowSourceName = count($sourceIds) > 1 || Arr::first($sourceIds) === 'all';
        $shouldShowWebmasterName = count($webmasterIds) > 1 || Arr::first($webmasterIds) === 'all';

        return view('admin.reports.revenue', compact(
            'rows',
            'detailedRows',
            'webmasters',
            'sources',
            'shouldShowSourceName',
            'shouldShowWebmasterName',
            'shouldShowSalary'
        ));
    }
}