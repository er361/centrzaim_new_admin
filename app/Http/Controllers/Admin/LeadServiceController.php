<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadService;
use App\Models\SmsProvider;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LeadServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|\Illuminate\Contracts\View\View|Application
    {
        if (!Gate::allows('lead_service_access')) {
            abort(401);
        }

        $model = LeadService::all();

        return view('admin.lead-services.index', compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LeadService $leadService
     * @return Application|Factory|View
     */
    public function edit(LeadService $leadService): Factory|View|Application
    {
        if (!Gate::allows('lead_service_edit')) {
            abort(401);
        }

        $model = $leadService;

        return view('admin.lead-services.edit', compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param LeadService $leadService
     * @return RedirectResponse
     */
    public function update(Request $request, LeadService $leadService): RedirectResponse
    {
        if (!Gate::allows('lead_service_edit')) {
            abort(401);
        }

        $leadService->update($request->all());

        return redirect()->route('admin.lead-services.index');
    }
}
