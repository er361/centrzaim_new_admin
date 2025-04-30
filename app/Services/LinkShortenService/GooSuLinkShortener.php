<?php

namespace App\Services\LinkShortenService;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GooSuLinkShortener implements LinkShortenServiceContract
{
    private string $url;
    private string $key;
    private $httpClient;

    public function __construct()
    {
        $this->url = config('services.goosuShortener.url');
        $this->key = config('services.goosuShortener.key');
        // Проверка на окружение 'local' для использования прокси
        $options = app()->environment('local') ? ['proxy' => 'http://seaboy820fK95:mvuveH3Gux@45.134.12.194:51523'] : [];
        $this->httpClient = Http::withOptions($options)->withHeader('x-goo-api-token', $this->key);
    }

    public function get(string $link): string
    {
        $res = $this->httpClient
            ->post($this->url . '/links/create', ['url' => $link]);

        if ($res->failed() || !$res->json()['successful']) {
            Log::channel('services')->error('GooSuLinkShortener error', ['response' => $res->body()]);
            return $link;
        }

        return $res->json()['short_url'];
    }

    public function getDelay(): int
    {
        return 0;
    }
}