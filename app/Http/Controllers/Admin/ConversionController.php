<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversion;
use App\Models\Source;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class ConversionController extends Controller
{
    /**
     * Display a listing of User.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if (!Gate::allows('conversion_access')) {
            abort(401);
        }

        if ($request->ajax()) {
            return $this->indexAjax($request);
        }

        $sources = Source::query()->get();

        return view('admin.conversions.index', compact('sources'));
    }

    protected function indexAjax(Request $request)
    {
        $query = Conversion::query();
        $query->select([
            'conversions.id',
            'conversions.source_id',
            'conversions.api_offer_id',
            'conversions.api_conversion_id',
            'conversions.api_created_at',
            'conversions.api_status',
            'conversions.api_payout',
        ])
            ->with([
                'source',
            ]);

        if ($request->has('date_from')) {
            $query->where('api_created_at', '>=', Carbon::parse($request->input('date_from'))->startOfDay());
        }

        if($request->has('webmaster_id')){
            $query->whereHas('source', function ($query) use ($request) {
                $query->where('webmaster_id', $request->input('webmaster_id'));
            });
        }

        if ($request->has('date_to')) {
            $query->where('api_created_at', '<=', Carbon::parse($request->input('date_to'))->endOfDay());
        }

        if ($request->has('source_id')) {
            $query->where('source_id', $request->input('source_id'));
        }

        $table = Datatables::of($query);

        $table->editColumn('source_id', function (Conversion $conversion) {
            return $conversion->source->name ?? '-';
        });

        $table->editColumn('api_created_at', function (Conversion $conversion) {
            return $conversion->api_created_at?->format('d.m.Y H:i') ?? '-';
        });

        $table->editColumn('api_status', function (Conversion $conversion) {
            return $conversion->status_text;
        });

        $table->setRowAttr([
            'data-entry-id' => '{{$id}}',
        ]);

        return $table->make(true);
    }
}
