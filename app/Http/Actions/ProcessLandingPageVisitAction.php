<?php

namespace App\Http\Actions;

use App\Events\UserOnLandingPageEvent;
use App\Services\ActionService\ActionService;
use App\Services\SettingsService\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Cookie;

class ProcessLandingPageVisitAction
{
    /**
     * @var ActionService
     */
    protected ActionService $actionService;

    /**
     * @param ActionService $actionService
     */
    public function __construct(ActionService $actionService)
    {
        $this->actionService = $actionService;
    }

    /**
     * @param string $sourceName
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function handle(string $sourceName, Request $request): Response|RedirectResponse
    {
        $sources = config('services.sources');
        $sources = collect($sources)->keyBy('route_key');

        if (!$sources->has($sourceName)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        event(new UserOnLandingPageEvent($request->all()));

        /** @var array $source */
        $source = $sources->get($sourceName);

        $cookieLifetime = $source['cookie_lifetime'];
        $sourceId = $source['source_id'];

        $webmasterKey = $source['webmaster_key'] ?? null;
        $webmasterCookie = null;

        $transactionKey = $source['transaction_key'] ?? null;
        $transactionCookie = null;

        $additionalTransactionKey = $source['additional_transaction_key'] ?? null;
        $additionalTransactionCookie = null;

        if ($webmasterKey !== null) {
            $webmasterCookie = $this->getWebmasterCookie(
                $request,
                $webmasterKey,
                $transactionKey,
                $sourceId,
                $cookieLifetime
            );
        }

        if ($transactionKey !== null) {
            $transactionCookie = $this->getTransactionCookie(
                $request,
                $transactionKey,
                $cookieLifetime
            );
        }

        if ($additionalTransactionKey !== null) {
            $additionalTransactionCookie = $this->getAdditionalTransactionCookie(
                $request,
                $additionalTransactionKey,
                $cookieLifetime
            );
        }

        $cookies = [$webmasterCookie, $transactionCookie, $additionalTransactionCookie];
        $cookies = array_filter($cookies);

        return $this->getResponse($cookies, $sourceName, $webmasterKey, $request->query());
    }

    /**
     * @param Cookie[] $cookies Массив cookie для установки пользователю
     * @param string $source ПП - источник пользователя
     * @param string|null $webmasterKey Ключ идентификатора вебмастера в query параметрах
     * @param array $query
     * @return RedirectResponse
     */
    protected function getResponse(array $cookies, string $source, ?string $webmasterKey, array $query): RedirectResponse
    {
        if (!isset($query['utm_source'])){
            $query['utm_source'] = $source;
        }

        if (!isset($query['utm_content']) && $webmasterKey !== null && isset($query[$webmasterKey])) {
            $query['utm_content'] = $query[$webmasterKey];
        }

        /** @var SettingsService $settingsService */
        $settingsService = App::make(SettingsService::class);

        if ($settingsService->shouldRedirectToRegisterPageFromSources()) {
            $route = route('auth.register', $query);
        } else {
            $route = route('front.index', $query);
        }

        return (new RedirectResponse($route))
            ->withCookies($cookies);
    }

    /**
     * Получить cookie вебмастера.
     * @param Request $request Запрос
     * @param string $webmasterKey Ключ в запросе с идентификтаором вебмастера
     * @param null|string $transactionKey Ключ в запросе с идентификатором транзакции
     * @param int $sourceId Идентификатор источника запроса
     * @param int $cookieLifetime Время жизни cookie в секундах
     * @return Cookie|null
     */
    protected function getWebmasterCookie(Request $request, string $webmasterKey, ?string $transactionKey, int $sourceId, int $cookieLifetime): ?Cookie
    {
        if (!$request->has($webmasterKey)) {
            return null;
        }

        $action = $this->actionService
            ->registerAction(
                $sourceId,
                $request->input($webmasterKey),
                $request->ip(),
                $request->userAgent(),
                !empty($transactionKey) ? $request->input($transactionKey) : null
            );

        return cookie(
            'webmaster_id',
            (string)$action->webmaster_id,
            $cookieLifetime / 60
        );
    }


    /**
     * Получить транзакционную cookie из запроса.
     * @param Request $request
     * @param string $transactionKey Ключ в запросе с идентификатором транзакции
     * @param int $cookieLifetime Время жизни cookie в секундах
     * @return Cookie|null
     */
    protected function getTransactionCookie(Request $request, string $transactionKey, int $cookieLifetime): ?Cookie
    {
        if (!$request->has($transactionKey)) {
            return null;
        }

        return cookie(
            'transaction_id',
            $request->input($transactionKey),
            $cookieLifetime / 60
        );
    }

    /**
     * Получить дополнительную транзакционную cookie из запроса.
     * @param Request $request
     * @param string $additionalTransactionKey Ключ в запросе с идентификатором транзакции
     * @param int $cookieLifetime Время жизни cookie в секундах
     * @return Cookie|null
     */
    protected function getAdditionalTransactionCookie(Request $request, string $additionalTransactionKey, int $cookieLifetime): ?Cookie
    {
        if (!$request->has($additionalTransactionKey)) {
            return null;
        }

        return cookie(
            'additional_transaction_id',
            $request->input($additionalTransactionKey),
            $cookieLifetime / 60
        );
    }
}