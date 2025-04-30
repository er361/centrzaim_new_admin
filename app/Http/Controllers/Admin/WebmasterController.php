<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WebmasterStoreRequest;
use App\Http\Requests\Admin\WebmasterUpdateRequest;
use App\Jobs\ExportStatistics;
use App\Models\Source;
use App\Models\Statistic;
use App\Models\Webmaster;
use App\Services\PostbackService\PostbackServiceStepDecider;
use App\Services\ReportService\RevenueReportStatisticsExportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class WebmasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @throws Exception
     */
    public function index(Request $request)
    {
        if (!Gate::allows('webmaster_access')) {
            abort(401);
        }

        if ($request->ajax()) {
            return $this->indexAjax();
        }

        return view('admin.webmasters.index');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function indexAjax()
    {
        $query = Webmaster::query()
            ->select([
                'webmasters.id',
                'webmasters.source_id',
                'webmasters.api_id',
                'webmasters.income_percent',
                'webmasters.comment',
                'webmasters.postback_cost'
            ])
            ->with('source');
        $table = Datatables::of($query);

        $table->setRowAttr([
            'data-entry-id' => '{{$id}}',
        ]);

        $table->editColumn('source_id', function ($row) {
            /** @var Webmaster $row */
            return $row->source->name;
        });

        $table->addColumn('actions', '&nbsp;');
        $table->editColumn('actions', function ($row) {
            $gateKey = 'webmaster_';
            $routeKey = 'admin.webmasters';

            return view('admin.webmasters.actionsTemplate', compact('row', 'gateKey', 'routeKey'));
        });

        $table->rawColumns(['actions']);

        return $table->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param Webmaster $webmaster
     * @return Application|Factory|View
     */
    public function show(Webmaster $webmaster)
    {
        if (!Gate::allows('webmaster_view')) {
            abort(401);
        }

        $sourcesConfig = config('services.sources');
        $sourceConfig = Arr::first($sourcesConfig, function (array $sourceConfig) use ($webmaster) {
            return $sourceConfig['source_id'] === $webmaster['source_id'];
        });

        $link = route('front.lp.index', [
            'source' => $sourceConfig['route_key'],
            $sourceConfig['webmaster_key'] => $webmaster->api_id,
        ]);

        return view('admin.webmasters.show', compact('webmaster', 'link'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        if (!Gate::allows('webmaster_create')) {
            abort(401);
        }

        $sources = Source::all()->pluck('name', 'id');
        $postbackSteps = PostbackServiceStepDecider::STEPS;

        return view('admin.webmasters.create', compact('sources', 'postbackSteps'));
    }

    /**
     * @param WebmasterStoreRequest $request
     * @return RedirectResponse
     */
    public function store(WebmasterStoreRequest $request): RedirectResponse
    {
        if (!Gate::allows('webmaster_create')) {
            abort(401);
        }

        Webmaster::query()->create($request->validated());

        return redirect()->route('admin.webmasters.index');
    }

    /**
     * @param Webmaster $webmaster
     * @return Application|Factory|View
     */
    public function edit(Webmaster $webmaster): Factory|View|Application
    {
        if (!Gate::allows('webmaster_edit')) {
            abort(401);
        }

        $postbackSteps = PostbackServiceStepDecider::STEPS;

        return view('admin.webmasters.edit', compact('webmaster', 'postbackSteps'));
    }

    /**
     * @param WebmasterUpdateRequest $request
     * @param Webmaster $webmaster
     * @param RevenueReportStatisticsExportService $exportService
     * @return RedirectResponse
     */
    public function update(WebmasterUpdateRequest $request, Webmaster $webmaster, RevenueReportStatisticsExportService $exportService): RedirectResponse
    {
        if (!Gate::allows('webmaster_edit')) {
            abort(401);
        }

        $webmaster->update($request->validated());

        if ($webmaster->wasChanged('income_percent')) {
            $relatedStatistic = Statistic::query()
                ->where('webmaster_id', $webmaster->id);

            $relatedStatisticMinDate = $relatedStatistic->clone()->min('date');
            $relatedStatisticMaxDate = $relatedStatistic->clone()->max('date');

            $relatedStatistic->update([
                'webmaster_income_coefficient' => $webmaster->income_percent,
            ]);

            if ($relatedStatisticMinDate !== null && $relatedStatisticMaxDate !== null) {
                $relatedStatisticMinDate = Carbon::parse($relatedStatisticMinDate);
                $relatedStatisticMaxDate = Carbon::parse($relatedStatisticMaxDate);

                dispatch(new ExportStatistics(
                    $relatedStatisticMinDate,
                    $relatedStatisticMaxDate
                ));
            }
        }

        return redirect()->route('admin.webmasters.index');
    }
}
