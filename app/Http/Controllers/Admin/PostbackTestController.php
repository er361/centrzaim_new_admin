<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostbackTestIndexRequest;
use App\Http\Requests\Admin\PostbackTestStoreRequest;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use App\Services\PostbackService\PostbackServiceFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class PostbackTestController extends Controller
{
    /** @var int Максимальное количество редиректов при переходе по ссылке */
    protected const MAX_REDIRECTS_COUNT = 10;

    public function index(PostbackTestIndexRequest $request): \Illuminate\Contracts\View\View
    {
        $sources = Source::query()
            ->pluck('name', 'id');

        return View::make('admin.postbacks.test', compact('sources'));
    }

    public function store(PostbackTestStoreRequest $request): RedirectResponse
    {
        $source = Source::query()
            ->find($request->integer('source_id'));
        $url = $request->validated('link');

        $sourceConfiguration = collect(config('services.sources'))
            ->first(fn(array $configuration) => $configuration['source_id'] === $source->id);

        $requestCount = 0;
        $redirectedUrl = $url;

        while ($requestCount < self::MAX_REDIRECTS_COUNT && !Str::contains($redirectedUrl, config('app.url'))) {
            $requestCount++;

            // Get new redirect URL
            $response = Http::withOptions([
                'allow_redirects' => false,
            ])->get($redirectedUrl);

            if ($response->status() >= 300 && $response->status() < 400) {
                $redirectedUrl = $response->header('Location');
            } else {
                break;
            }
        }

        if (!Str::contains($redirectedUrl, config('app.url'))) {
            return back()->withInput()
                ->withErrors([
                    'link' => 'При переходе по ссылке не происходит редирект на домен сайта. Проверьте ссылку и попробуйте еще раз. Последний адрес переадресации: ' . $redirectedUrl,
                ]);
        }

        $query = parse_url($redirectedUrl, PHP_URL_QUERY);
        parse_str($query, $query);

        $webmasterKey = $query[$sourceConfiguration['webmaster_key']] ?? null;
        $transactionKey = $query[$sourceConfiguration['transaction_key']]  ?? null;
        $additionalTransactionKey = isset($sourceConfiguration['additional_transaction_key'])
            ? $query[$sourceConfiguration['additional_transaction_key']]
            : null;

        if ($webmasterKey === null || $transactionKey === null) {
            return back()->withInput()
                ->withErrors([
                    'link' => 'Не смогли распознать данные по ссылке. Проверьте ссылку, правильность выбора партнерской программы и попробуйте еще раз. Последний адрес переадресации: ' . $redirectedUrl,
                ]);
        }

        $webmaster = Webmaster::query()
            ->firstOrCreate([
                'source_id' => $source->id,
                'api_id' => $webmasterKey,
            ]);

        /** @var PostbackServiceFactory $postbackServiceFactory */
        $postbackServiceFactory = App::make(PostbackServiceFactory::class);
        $postbackService = $postbackServiceFactory->createPostbackService($source->id);

        /** @var User $user */
        $user = Auth::user();

        // Поля не нужно очищать, так как не сохраняем в базе
        $user->webmaster_id = $webmaster->id;
        $user->transaction_id = $transactionKey;
        $user->additional_transaction_id = $additionalTransactionKey;

        $postbackService->getPostbackNotifyService()->send($user);

        $successMessage = 'Тестовый постбэк был успешно отправлен. Webmaster ID = ' . $webmasterKey
            . ', Transaction ID = ' . $transactionKey
            . ', Additional Transaction ID = ' . ($additionalTransactionKey ?? '-')
            . '.';

        return back()->withInput()
            ->with('success', $successMessage);
    }
}