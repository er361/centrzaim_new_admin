<?php

namespace App\Services\LinkShortenService;

use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadsLinkShortener implements LinkShortenServiceContract
{

    private $delay = 0;

    public function __construct()
    {
        $this->logger = Log::channel('link_shortener');
        $this->url = config('services.leadShortener.api_url');
        $this->token = SettingsService::getByKey(SettingNameEnum::LeadsUrlShortenerToken) ?? config('services.leadShortener.token');
    }

    public function get(string $link): string
    {
        $fullUrl = $this->url . '?token=' . $this->token;

        $response = Http::post($fullUrl, [
            $link
        ]);

        if ($response->failed()) {
            throw new Exception(sprintf('Failed to shorten link: %s', $response->body()));
        }

        if ($response->json('success') !== true) {
            throw new Exception(sprintf('Failed to shorten link: %s', $response->body()));
        }

        if ($response->json('result.0.shorted_urls.0') === null) {
            throw new Exception(sprintf('Failed to shorten link: %s', $response->body()));
        }

        $this->logger->debug('Response from shortener: ' . $response->body());

        $shorted = $response->json('result.0.shorted_urls.0');
        return $shorted;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }
}