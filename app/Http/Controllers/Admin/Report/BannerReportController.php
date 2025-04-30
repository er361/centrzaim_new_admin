<?php


namespace App\Http\Controllers\Admin\Report;


use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use App\Services\ReportService\BannerReportService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class BannerReportController extends Controller
{
    /**
     * @param Request $request
     * @return Application|Factory|View
     * @throws AuthorizationException
     * @todo Разделить на отдельные методы
     */
    public function index(Request $request)
    {
        if (!Gate::allows('banner_report_access')) {
            abort(401);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
            $dateTo = Carbon::parse($request->input('date_to'))->endOfDay();

            $position = empty($request->input('position')) ? null : $request->input('position');
            $bannerId = empty($request->input('banner_id')) ? null : $request->input('banner_id');
            $sourceId = empty($request->input('source_id')) ? null : $request->input('source_id');
            $webmasterId = empty($request->input('webmaster_id')) ? null : $request->input('webmaster_id');
            $groupType = empty($request->input('group_type')) ? null : (int)$request->input('group_type');

            if (is_numeric($webmasterId)) {
                $webmaster = Webmaster::query()->find($webmasterId);
                $this->authorize('view', $webmaster);
            }

            /** @var User $user */
            $user = Auth::user();

            $service = new BannerReportService(
                $dateFrom,
                $dateTo,
                $position,
                $bannerId,
                $sourceId,
                $webmasterId,
                $groupType,
                $user
            );

            $rows = collect($service->getBanners());
            $rows = $rows->map(function(array $row) {
                $row['formatted_date'] = $this->getCarbonDate($row['date']);
                return $row;
            });

            $rows = $rows->sortBy('formatted_date');
        } else {
            $rows = collect();
        }

        /** @var User $user */
        $user = Auth::user();
        $sources = Source::query()
            ->forUser($user)
            ->get()
            ->keyBy('id');
        $banners = Banner::query()
            ->get()
            ->keyBy('id');
        $positions = Banner::POSITIONS;
        $webmasters = Webmaster::query()
            ->forUser($user)
            ->with('source')
            ->get()
            ->keyBy('api_id');
        return view('admin.reports.banner', compact(
            'rows',
            'webmasters',
            'sources',
            'positions',
            'banners'
        ));
    }

    /**
     * @param string $date
     * @return string
     * @todo Костыль для сортировки дат, подумать, как обойтись без него
     */
    protected function getCarbonDate(string $date): string
    {
        // 08.2020 или 01.08.2020
        if (Str::length($date) === 7) {
            return Carbon::parse('01.' . $date)->toDateString();
        }

        return Carbon::parse($date)->toDateString();
    }
}