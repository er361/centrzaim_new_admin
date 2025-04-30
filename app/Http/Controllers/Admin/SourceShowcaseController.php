<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SourceShowcaseStoreRequest;
use App\Models\SourceShowcase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class SourceShowcaseController extends Controller
{
    /**
     * Сохраняет SourceShowcase.
     *
     * @param SourceShowcaseStoreRequest $request
     * @return RedirectResponse
     */
    public function store(SourceShowcaseStoreRequest $request): RedirectResponse
    {

        SourceShowcase::query()->updateOrCreate(
            [
                'showcase_id' => $request->input('showcase_id'),
                'source_id' => $request->input('source_id'),
                'webmaster_id' => $request->input('webmaster_id'),
            ],
            $request->validated()
        );

        return Redirect::back()->with('success', 'Настройки витрины успешно обновлены');
    }
}
