<?php


namespace App\Http\Controllers\Front;

use App\Http\Actions\ProcessLandingPageVisitAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SourcePageController extends Controller
{
    /**
     * @param string $source
     * @param Request $request
     * @param ProcessLandingPageVisitAction $action
     * @return Response|RedirectResponse
     */
    public function __invoke(string $source, Request $request, ProcessLandingPageVisitAction $action): Response|RedirectResponse
    {
        return $action->handle(
            $source,
            $request
        );
    }
}