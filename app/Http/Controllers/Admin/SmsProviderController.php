<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsProvider;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SmsProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Gate::allows('sms_provider_access')) {
            abort(401);
        }

        $model = SmsProvider::all();
        $services = SmsProvider::SERVICES;

        return view('admin.sms-providers.index', compact('model', 'services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Gate::allows('sms_provider_create')) {
            abort(401);
        }

        $services = SmsProvider::SERVICES;
        return view('admin.sms-providers.create', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Gate::allows('sms_provider_create')) {
            abort(401);
        }

        SmsProvider::query()->create($request->all());

        return redirect()->route('admin.sms-providers.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SmsProvider $smsProvider
     * @return Application|Factory|View
     */
    public function edit(SmsProvider $smsProvider)
    {
        if (!Gate::allows('sms_provider_edit')) {
            abort(401);
        }

        $model = $smsProvider;
        $services = SmsProvider::SERVICES;

        return view('admin.sms-providers.edit', compact('model', 'services'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param SmsProvider $smsProvider
     * @return RedirectResponse
     */
    public function update(Request $request, SmsProvider $smsProvider): RedirectResponse
    {
        if (!Gate::allows('sms_provider_edit')) {
            abort(401);
        }

        $smsProvider->update($request->all());

        return redirect()->route('admin.sms-providers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SmsProvider $smsProvider
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(SmsProvider $smsProvider): RedirectResponse
    {
        if (!Gate::allows('sms_provider_delete')) {
            abort(401);
        }

        $smsProvider->delete();
        return redirect()->route('admin.sms-providers.index');
    }
}
