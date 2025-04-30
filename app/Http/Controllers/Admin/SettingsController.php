<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsStoreRequest;
use App\Services\PostbackService\PostbackServiceStepDecider;
use App\Services\SettingsService\SettingsService;
use App\Services\SiteService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display a listing of Setting.
     *
     * @return Response
     */
    public function index(): Response
    {
        if (!Gate::allows('setting_access')) {
            abort(401);
        }

        $settings = setting()->all();

        $config = SiteService::getActiveSiteConfiguration();
        $steps = collect($config['fill_steps'])
            ->mapWithKeys(function (array $fields, int $step) {
                $fieldsString = collect($fields)
                    ->filter(function (array $rules, string $name) {
                        return $name !== 'fill_step';
                    })
                    ->map(function (array $rules, string $name) {
                        return __('validation.attributes.' . $name);
                    })
                    ->implode(', ');

                return [$step => $fieldsString];
            })
            ->toArray();

        $postbackSteps = PostbackServiceStepDecider::STEPS;

        return response()->view('admin.settings.index', compact(
            'settings',
            'steps',
            'postbackSteps'
        ));
    }

    /**
     * Store a newly created Setting in storage.
     *
     * @param SettingsStoreRequest $request
     * @return RedirectResponse
     */
    public function store(SettingsStoreRequest $request)
    {
        if (!Gate::allows('setting_edit')) {
            abort(401);
        }

        setting($request->validated())->save();

        return redirect()->back();
    }
}
