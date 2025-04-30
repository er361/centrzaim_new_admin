<?php


namespace App\Services\LinkShortenService;


use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class CuttLyLinkShortener implements LinkShortenServiceContract
{
    /**
     * @var string
     */
    protected string $apiKey;

    /**
     * Задержка между сокращением ссылок, в секундах
     *
     * @var int
     */
    protected int $delay;

    /**
     * CuttLyLinkShortener constructor.
     */
    public function __construct()
    {
        $this->apiKey = SettingsService::getByKey(SettingNameEnum::CuttLyApiKey);
        $this->delay = (int)SettingsService::getByKey(SettingNameEnum::CuttLyDelay);
    }

    /**
     * Получить короткую версию ссылки.
     * @param string $link
     * @return string
     * @throws RequestException
     */
    public function get(string $link): string
    {
        $query = [
            'key' => $this->apiKey,
            'short' => $link,
        ];

        return Http::get('https://cutt.ly/api/api.php', $query)
            ->throw()
            ->json('url.shortLink');
    }

    /**
     * Получить задержку в секундах между возможностью сокращения ссылок.
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }
}