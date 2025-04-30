<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PaymentsController extends Controller
{
    /**
     * Display a listing of Payment.
     *
     * @param Request $request
     * @return Application|Factory|Response|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if (!Gate::allows('payment_access')) {
            return abort(401);
        }

        if ($request->ajax()) {
            return $this->indexAjax($request);
        }

        $types = Payment::TYPES;
        $statuses = Payment::STATUSES;
        $paymentPlans = collect(config('payments.plans'))
            ->pluck('name')
            ->toArray();

        return view('admin.payments.index', compact('types', 'statuses', 'paymentPlans'));
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function indexAjax(Request $request)
    {
        $query = Payment::query()
            ->select([
                'payments.id',
                'payments.amount',
                'payments.status',
                'payments.type',
                'payments.subtype',
                'payments.payment_number',
                'payments.iteration_number',
                'payments.service',
                'payments.created_at',
                'payments.error_code',
                'users.payment_plan',
            ])
            ->filter($request->all())
            ->join('users', 'users.id', '=', 'payments.user_id');

        $table = Datatables::of($query);

        $table->setRowAttr([
            'data-entry-id' => '{{$id}}',
        ]);
        $table->addColumn('actions', '&nbsp;');
        $template = 'admin.actionsTemplate';
        $table->editColumn('actions', function ($row) use ($template) {
            $gateKey = 'payment_';
            $routeKey = 'admin.payments';

            return view($template, compact('row', 'gateKey', 'routeKey'));
        });

        $table->editColumn('payment_number', function (Payment $row) {
            return $row->payment_number + 1;
        });

        $table->editColumn('iteration_number', function (Payment $row) {
            return $row->iteration_number + 1;
        });

        $paymentPlans = config('payments.plans');
        $table->editColumn('payment_plan', function (Payment $row) use ($paymentPlans) {
            return $paymentPlans[$row->getAttribute('payment_plan')]['name'] ?? '-';
        });

        $table->editColumn('amount', function (Payment $row) {
            return $row->amount . ' ₽';
        });

        $table->editColumn('created_at', function ($row) {
            return $row->created_at->format('d.m.Y H:i:s');
        });

        $table->editColumn('status', function (Payment $row) {
            return $row->status_description;
        });

        $table->editColumn('subtype', function (Payment $row) {
            return $row->subtype_description;
        });

        $table->editColumn('type', function (Payment $row) {
            return $row->type_description;
        });

        $table->editColumn('service', function (Payment $row) {
            return $row->service_description;
        });

        $table->rawColumns(['actions']);

        return $table->make(true);
    }

    /**
     * Display Payment.
     *
     * @param Payment $payment
     * @return Application|Factory|Response|View
     */
    public function show(Payment $payment)
    {
        if (!Gate::allows('payment_view')) {
            return abort(401);
        }

        return view('admin.payments.show', compact('payment'));
    }
}
