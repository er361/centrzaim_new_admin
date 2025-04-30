<?php


namespace App\Services\LinkShortenService;


class NullLinkShortener implements LinkShortenServiceContract
{
    /**
     * Получить короткую версию ссылки.
     * @param string $link
     * @return string
     */
    public function get(string $link): string
    {
        return $link;
    }

    /**
     * Получить задержку в секундах между возможностью сокращения ссылок.
     * @return int
     */
    public function getDelay(): int
    {
        return 0;
    }
}