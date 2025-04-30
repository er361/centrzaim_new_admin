<?php


namespace App\Http\Controllers\Admin\Report;


use App\Http\Controllers\Controller;
use App\Models\Sms;
use App\Models\User;
use App\Services\ReportService\SmsReportService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class SmsReportController extends Controller
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

            $smsId = empty($request->input('sms_id')) ? null : $request->input('sms_id');
            $groupType = empty($request->input('group_type')) ? null : (int)$request->input('group_type');

            /** @var User $user */
            $user = Auth::user();

            $service = new SmsReportService(
                $dateFrom,
                $dateTo,
                $smsId,
                $groupType,
                $user
            );

            $rows = collect($service->getSmsStatistics());
            $rows = $rows->map(function(array $row) {
                $row['formatted_date'] = $this->getCarbonDate($row['date']);
                return $row;
            });

            $rows = $rows->sortBy('formatted_date');
        } else {
            $rows = collect();
        }

        $sms = Sms::query()
            ->get()
            ->keyBy('id');

        return view('admin.reports.sms', compact(
            'rows',
            'sms',
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