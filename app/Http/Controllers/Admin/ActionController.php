<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class ActionController extends Controller
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
        if (!Gate::allows('action_access')) {
            abort(401);
        }

        if ($request->ajax()) {
            $query = Action::query();
            $query->select([
                'actions.id',
                'actions.ip',
                'actions.user_agent',
            ]);

            if ($request->has('webmaster_id')) {
                $query->where('webmaster_id', $request->input('webmaster_id'));
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', Carbon::parse($request->input('date_from')));
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', Carbon::parse($request->input('date_to')));
            }

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);

            return $table->make(true);
        }

        return view('admin.actions.index');
    }
}
