<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerIndexRequest;
use App\Http\Requests\Admin\BannerStoreRequest;
use App\Http\Requests\Admin\BannerUpdateRequest;
use App\Models\Banner;
use App\Models\Source;
use App\Models\Webmaster;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param BannerIndexRequest $request
     * @return Factory|Application|View|JsonResponse
     * @throws Exception
     */
    public function index(BannerIndexRequest $request): Factory|Application|View|JsonResponse
    {
        if ($request->ajax()) {
            $query = Banner::query()
                ->select([
                    'banners.id',
                    'banners.name',
                    'banners.position',
                ]);

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);

            $template = 'admin.actionsTemplate';
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey = 'banner_';
                $routeKey = 'admin.banners';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });

            $table->rawColumns(['actions']);

            return $table->make(true);
        }

        return view('admin.banners.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\View|Factory|\Illuminate\Contracts\Foundation\Application
    {
        if (!Gate::allows('banner_create')) {
            abort(401);
        }

        $positions = Banner::POSITIONS;
        $sources = Source::query()->get()->pluck('name', 'id');
        $webmasters = Webmaster::query()
            ->with('source')
            ->get()
            ->keyBy('id')
            ->map(function (Webmaster $webmaster) {
                return $webmaster->completeName;
            });

        return view('admin.banners.create', compact('sources', 'webmasters', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BannerStoreRequest $request
     * @return RedirectResponse
     */
    public function store(BannerStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $banner = Banner::query()->create($data);
        $banner->sources()->sync($request->input('source_id'));
        $banner->webmasters()->sync($request->input('webmaster_id'));

        return redirect()->route('admin.banners.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Banner $banner
     * @return Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View
     */
    public function edit(Banner $banner)
    {
        if (!Gate::allows('banner_edit')) {
            abort(401);
        }

        $positions = Banner::POSITIONS;
        $sources = Source::query()->get()->pluck('name', 'id');
        $webmasters = Webmaster::query()
            ->with('source')
            ->get()
            ->keyBy('id')
            ->map(function (Webmaster $webmaster) {
                return $webmaster->completeName;
            });

        return view(
            'admin.banners.edit',
            compact(
                'banner',
                'positions',
                'sources',
                'webmasters',
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BannerUpdateRequest $request
     * @param Banner $banner
     * @return RedirectResponse
     */
    public function update(BannerUpdateRequest $request, Banner $banner): RedirectResponse
    {
        $data = $request->validated();

        $banner->update($data);
        $banner->sources()->sync($request->input('source_id'));
        $banner->webmasters()->sync($request->input('webmaster_id'));

        return redirect()->route('admin.banners.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Banner $banner
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Banner $banner)
    {
        if (!Gate::allows('banner_delete')) {
            abort(401);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index');
    }
}
