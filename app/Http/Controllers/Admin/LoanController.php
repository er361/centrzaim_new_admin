<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoanIndexRequest;
use App\Http\Requests\Admin\LoanStoreRequest;
use App\Http\Requests\Admin\LoanUpdateRequest;
use App\Models\Loan;
use App\Models\LoanLink;
use App\Models\Showcase;
use App\Models\Source;
use App\Models\Webmaster;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param LoanIndexRequest $request
     * @return Factory|Application|View|JsonResponse
     * @throws Exception
     */
    public function index(LoanIndexRequest $request): Factory|Application|View|JsonResponse
    {
        if ($request->ajax()) {
            $query = Loan::query()
                ->join('sources', 'loans.source_id', '=', 'sources.id')
                ->select([
                    'loans.id',
                    'loans.name',
                    'loans.api_id',
                    'sources.name as source_name'
                ]);

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);

            $template = 'admin.actionsTemplate';
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey = 'loan_';
                $routeKey = 'admin.loans';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });

            $table->rawColumns(['actions']);

            return $table->make(true);
        }

        return view('admin.loans.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\View|Factory|\Illuminate\Contracts\Foundation\Application
    {
        if (!Gate::allows('loan_create')) {
            return abort(401);
        }

        $shouldShowExtendedFields = config('services.showcases.is_extended');

        return view('admin.loans.create', compact('shouldShowExtendedFields'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LoanStoreRequest $request
     * @return RedirectResponse
     */
    public function store(LoanStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('loans', 'public');
            unset($data['image']);
        }

        $loan = Loan::query()->create($data);

        return redirect()->route('admin.loans.edit', $loan);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Loan $loan
     * @return Factory|Application|View|void
     */
    public function edit(Loan $loan)
    {
        if (!Gate::allows('loan_edit')) {
            abort(401);
        }

        /** @var Collection<int, LoanLink> $loanLinks */
        $loanLinks = $loan->loanLinks()
            ->with('source')
            ->get()
            ->keyBy('source_id');

        $loanOffersOptions = $loanLinks
            ->mapWithKeys(function (LoanLink $loanLink, int $i) {
                return [$loanLink->id => $loanLink->source->name];
            })
            ->prepend('Не показывать', null);

        $loanOffers = $loan->loanOffers
            ->whereNull('webmaster_id')
            ->groupBy([
                'source_id',
                'showcase_id',
            ]);

        $sources = Source::query()->get();
        $showcases = Showcase::query()->get();
        $webmasters = Webmaster::query()->get();
        $shouldShowExtendedFields = config('services.showcases.is_extended');

        return view(
            'admin.loans.edit',
            compact(
                'loan',
                'loanLinks',
                'loanOffersOptions',
                'loanOffers',
                'sources',
                'showcases',
                'shouldShowExtendedFields',
                'webmasters'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LoanUpdateRequest $request
     * @param Loan $loan
     * @return RedirectResponse
     */
    public function update(LoanUpdateRequest $request, Loan $loan): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('loans', 'public');
            unset($data['image']);
        }

        $loan->update($data);

        return redirect()->route('admin.loans.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Loan $loan
     * @return RedirectResponse|void
     * @throws Exception
     */
    public function destroy(Loan $loan)
    {
        if (!Gate::allows('loan_delete')) {
            return abort(401);
        }

        $loan->delete();

        return redirect()->route('admin.loans.index');
    }
}
