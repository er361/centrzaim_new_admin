<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SourceUpdateRequest;
use App\Models\Source;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        if (!Gate::allows('source_access')) {
            abort(401);
        }

        $sources = Source::query()->get();

        return view('admin.sources.index', compact('sources'));
    }

    /**
     * @param Source $source
     * @return Application|Factory|View
     */
    public function edit(Source $source)
    {
        if (!Gate::allows('source_edit')) {
            abort(401);
        }

        return view('admin.sources.edit', compact('source'));
    }

    /**
     * @param SourceUpdateRequest $request
     * @param Source $source
     * @return RedirectResponse
     */
    public function update(SourceUpdateRequest $request, Source $source): RedirectResponse
    {
        if (!Gate::allows('source_edit')) {
            abort(401);
        }

        $source->update($request->validated());
        return redirect()->route('admin.sources.index');
    }
}
