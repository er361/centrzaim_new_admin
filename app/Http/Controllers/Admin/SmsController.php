<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SmsTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SmsStoreRequest;
use App\Http\Requests\Admin\SmsUpdateRequest;
use App\Models\Showcase;
use App\Models\Sms;
use App\Models\SmsClick;
use App\Models\SmsProvider;
use App\Models\SmsUser;
use App\Models\Source;
use App\Models\Webmaster;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SmsController extends Controller
{
    /**
     * Display a listing of Setting.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        if (!Gate::allows('sms_access')) {
            abort(401);
        }

        $model = Sms::query()
            ->with([
                'source',
                'smsProvider'
            ])
            ->get();

        return view('admin.sms.index', compact('model'));
    }

    /**
     * Show the form for creating new Setting.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        if (!Gate::allows('sms_create')) {
            abort(401);
        }

        $showcases = Showcase::query()->whereNotNull('external_url')->pluck('name', 'id');
        $providers = SmsProvider::all()->pluck('name', 'id');
        $sources = Source::query()->pluck('name', 'id');
        $types = SmsTypeEnum::getLabels();
        $relatedSms = Sms::query()->pluck('name', 'id');
        $webmasters = Webmaster::query()
            ->with('source')
            ->get()
            ->keyBy('id')
            ->map(fn(Webmaster $webmaster) => $webmaster->completeName);



        return view('admin.sms.create', compact(
            'providers',
            'sources',
            'types',
            'showcases',
            'webmasters',
            'relatedSms',
        ));
    }

    /**
     * Store a newly created Setting in storage.
     *
     * @param SmsStoreRequest $request
     * @return RedirectResponse
     */
    public function store(SmsStoreRequest $request): RedirectResponse
    {
        if (!Gate::allows('sms_create')) {
            abort(401);
        }

        $data = $request->validated();
        $data['registered_after'] = Carbon::createFromFormat('d.m.Y H:i', $data['registered_after']);

        /** @var Sms $sms */
        $sms = Sms::query()->create($data);
        $sms->includedWebmasters()->sync($request->validated('included_webmaster_id'));
        $sms->excludedWebmasters()->sync($request->validated('excluded_webmaster_id'));

        return redirect()->route('admin.sms.index');
    }


    /**
     * Show the form for editing Setting.
     *
     * @param Sms $sms
     * @return Application|Factory|View
     */
    public function edit(Sms $sms)
    {
        if (!Gate::allows('sms_edit')) {
            abort(401);
        }

        $model = $sms;
        $showcases = Showcase::query()->whereNotNull('external_url')->pluck('name', 'id');
        $providers = SmsProvider::all()->pluck('name', 'id');
        $sources = Source::query()->get()->pluck('name', 'id');
        $types = SmsTypeEnum::getLabels();
        $relatedSms = Sms::query()->pluck('name', 'id');
        $webmasters = Webmaster::query()
            ->with('source')
            ->get()
            ->keyBy('id')
            ->map(function (Webmaster $webmaster) {
                return $webmaster->completeName;
            });



        return view('admin.sms.edit', compact(
            'model',
            'providers',
            'sources',
            'types',
            'showcases',
            'webmasters',
            'relatedSms',
        ));
    }

    /**
     * Update Setting in storage.
     *
     * @param SmsUpdateRequest $request
     * @param Sms $sms
     * @return RedirectResponse
     */
    public function update(SmsUpdateRequest $request, Sms $sms): RedirectResponse
    {
        if (!Gate::allows('sms_edit')) {
            abort(401);
        }

        $data = $request->validated();
        $data['registered_after'] = Carbon::createFromFormat('d.m.Y H:i', $data['registered_after']);

        $sms->update($data);
        $sms->includedWebmasters()->sync($request->validated('included_webmaster_id'));
        $sms->excludedWebmasters()->sync($request->validated('excluded_webmaster_id'));

        return redirect()->route('admin.sms.index');
    }

    /**
     * Remove Setting from storage.
     *
     * @param Sms $sms
     * @return RedirectResponse
     */
    public function destroy(Sms $sms): RedirectResponse
    {
        if (!Gate::allows('sms_delete')) {
            abort(401);
        }

        $relatedToDeletable = Sms::query()
            ->where('related_sms_id', $sms->id)
            ->get();

        if ($relatedToDeletable->isNotEmpty()) {
            return redirect()->back()
                ->withErrors(['Нельзя удалить SMS, которые используются для SMS по касанию']);
        }

        $sms->delete();

        return redirect()->route('admin.sms.index');
    }

    /**
     * Просмотр SMS.
     * @param Sms $sms
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function show(Sms $sms)
    {
        $baseSmsUserQuery = SmsUser::query()
            ->where('sms_id', $sms->id);
        $baseSmsClickQuery = SmsClick::query()
            ->where('sms_id', $sms->id);
        $now = Carbon::now();

        $sentLastHourQuery = $baseSmsUserQuery
            ->clone()
            ->where('created_at', '>', $now);
        $sentLastHourCount = $sentLastHourQuery->count();
        $sentLastHourTotal = $sentLastHourQuery->sum('cost');
        $clicksLastHourCount = $baseSmsClickQuery
            ->clone()
            ->where('created_at', '>', $now)
            ->count();

        $sentLastDayQuery = $baseSmsUserQuery
            ->clone()
            ->where('created_at', '>', $now->subDay());
        $sentLastDayCount = $sentLastDayQuery->count();
        $sentLastDayTotal = $sentLastDayQuery->sum('cost');
        $clicksLastDayCount = $baseSmsClickQuery
            ->clone()
            ->where('created_at', '>', $now->subDay())
            ->count();

        $lastSends = $baseSmsUserQuery
            ->clone()
            ->orderByDesc('created_at')
            ->take(15)
            ->with([
                'user',
            ])
            ->get();

        $lastClicks = SmsClick::query()
            ->where('sms_id', $sms->id)
            ->orderByDesc('id')
            ->take(15)
            ->with([
                'user',
            ])
            ->get();

        return view('admin.sms.show', compact(
            'sms',
            'sentLastHourTotal',
            'sentLastHourCount',
            'sentLastDayTotal',
            'sentLastDayCount',
            'lastSends',
            'lastClicks',
            'clicksLastDayCount',
            'clicksLastHourCount'
        ));
    }

    /**
     * Delete all selected Setting at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (!Gate::allows('sms_delete')) {
            abort(401);
        }

        if ($request->input('ids')) {
            $relatedToDeletable = Sms::query()
                ->whereIn('related_sms_id', $request->input('ids'))
                ->get();

            if ($relatedToDeletable->isNotEmpty()) {
                return redirect()->back()
                    ->withErrors(['Нельзя удалить SMS, которые используются для SMS по касанию']);
            }

            $entries = Sms::query()
                ->whereIn('id', $request->input('ids'))
                ->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
