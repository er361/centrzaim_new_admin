<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebmasterTemplateRequest;
use App\Models\WebmasterTemplate;

class WebmasterTemplateController extends Controller
{
    public function __invoke(WebmasterTemplateRequest $request)
    {
        $showcaseId = $request->input('showcase_id');
        $sourceId = $request->input('source_id');

        WebmasterTemplate::where('showcase_id', $showcaseId)
            ->where('source_id', $sourceId)
            ->delete();

        WebmasterTemplate::create($request->validated());
        return redirect()->back();
    }
}
