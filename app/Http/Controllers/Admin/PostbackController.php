<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PostbackExport;
use App\Http\Controllers\Controller;
use App\Models\Postback;
use App\Models\Source;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

class PostbackController extends Controller
{
    /**
     * Display a listing of User.
     *
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        if (!Gate::allows('postback_access')) {
            abort(401);
        }

        if ($request->ajax()) {
            $query = Postback::query()
                ->filter($request->all())
                ->select([
                    'postbacks.id',
                    'postbacks.created_at',
                    'postbacks.user_id',
                    'postbacks.cost',
                ])
                ->with('user.webmaster.source');

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);

            $table->addColumn('source_name', '&nbsp;');
            $table->editColumn('source_name', function ($row) {
                return $row->user?->webmaster?->source?->name ?? '-';
            });

            $table->addColumn('webmaster_api_id', '&nbsp;');
            $table->editColumn('webmaster_api_id', function ($row) {
                return $row->user?->webmaster?->api_id ?? '-';
            });

            $table->addColumn('user_transaction_id', '&nbsp;');
            $table->editColumn('user_transaction_id', function ($row) {
                return $row->user?->transaction_id ?? '-';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at->format('d.m.Y H:i:s');
            });

            return $table->make(true);
        }

        $sources = Source::query()->get();

        return view('admin.postbacks.index', compact('sources'));
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function export(Request $request): BinaryFileResponse
    {
        if (!Gate::allows('postback_access')) {
            abort(401);
        }

        $query = Postback::query()
            ->filter($request->all())
            ->with([
                'user.webmaster.source',
                'user.action'
            ]);

        $fileName = 'postbacks_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PostbackExport($query), $fileName);
    }
}
