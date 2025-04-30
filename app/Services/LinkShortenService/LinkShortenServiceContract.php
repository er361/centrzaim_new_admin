<?php


namespace App\Services\LinkShortenService;


interface LinkShortenServiceContract
{
    /**
     * Получить короткую версию ссылки.
     * @param string $link
     * @return string
     */
    public function get(string $link): string;

    /**
     * Получить задержку в секундах между возможностью сокращения ссылок.
     * @return int
     */
    public function getDelay(): int;
}