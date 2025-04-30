<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Actions\ProcessPostbackAction;
use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PostbackController extends Controller
{
    /**
     * Сохранение входящего постбэка.
     *
     * @param Request $request
     * @param ProcessPostbackAction $action
     *
     * @return ResponseFactory|Application|Response
     */
    public function store(Request $request, ProcessPostbackAction $action): Application|Response|ResponseFactory
    {
        Log::debug('Пришел запрос на создание конверсии.', [
            'request' => json_encode($request->all()),
        ]);

        if (!$request->has('postback_secret')) {
            abort(400);
        }

        if ($request->input('postback_secret') !== config('app.postback_secret')) {
            abort(400);
        }

        /** @var Source|null $source */
        $source = Source::query()->find($request->input('source_id'));
        $action->handle($request, $source);

        return response('', Response::HTTP_OK);
    }
}
