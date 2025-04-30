<?php


namespace App\Http\Controllers\Admin\Report;


use App\Http\Controllers\Controller;
use App\Models\Statistic;
use App\Models\Webmaster;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DiffReportController extends Controller
{
    /**
     * @param Request $request
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        if (!Gate::allows('diff_report_access')) {
            abort(401);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $columns = (new Statistic())->getFillable();
            $column = $request->input('column');

            if (!in_array($column, $columns)) {
                abort(400, 'Invalid column');
            }

            $currentPeriodStartsAt = Carbon::parse($request->input('date_from'))->startOfDay();
            $currentPeriodEndsAt = Carbon::parse($request->input('date_to'))->endOfDay();
            $periodLength = $currentPeriodEndsAt->diffInDays($currentPeriodStartsAt);

            $previousPeriodEndsAt = $currentPeriodStartsAt->copy()->subDay()->endOfDay();
            $previousPeriodStartsAt = $previousPeriodEndsAt->copy()->subDays($periodLength)->startOfDay();

            $previousPeriodQuery = $this->getQuery($previousPeriodStartsAt, $previousPeriodEndsAt, $column);
            $currentPeriodQuery = $this->getQuery($currentPeriodStartsAt, $currentPeriodEndsAt, $column);

            $rows = Statistic::query()
                ->select([
                    'current.webmaster_id',
                    'current.data AS current_period',
                    'previous.data AS previous_period',
                    DB::raw( 'current.data - previous.data AS diff'),
                    DB::raw('ROUND(((current.data - previous.data) / previous.data) * 100, 2) AS diff_percent'),
                ])
                ->fromSub($currentPeriodQuery, 'current')
                ->joinSub($previousPeriodQuery, 'previous', function ($join) {
                    $join->on('current.webmaster_id', '=', 'previous.webmaster_id');
                }, type: 'left')
                ->get();
        } else {
            $rows = collect();
        }

        $webmasters = Webmaster::query()
            ->with('source')
            ->get()
            ->keyBy('id');

        return view('admin.reports.diff', compact(
            'rows',
            'webmasters',
        ));
    }

    protected function getQuery(CarbonInterface $startsAt, CarbonInterface $endsAt, string $column): Builder
    {
        return Statistic::query()
            ->select([
                'webmaster_id',
            ])
            ->selectRaw('SUM('.$column.') AS data')
            ->whereBetween('date', [
                $startsAt->toDateString(),
                $endsAt->toDateString(),
            ])
            ->groupBy([
                'webmaster_id',
            ]);
    }
}
