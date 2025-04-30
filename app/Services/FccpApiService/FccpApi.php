<?php

namespace App\Services\FccpApiService;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FccpApi
{
    private string $url;
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|mixed
     */
    private mixed $key;
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|mixed
     */
    private mixed $type;

    public function __construct()
    {
        $this->url = config('fccp.url');
        $this->key = config('fccp.key');
        $this->type = config('fccp.type');
    }

    /**
     * @throws \Exception
     */
    public function searchFiz(string $firstName, string $lastName, string $patronymic, string $dateOfBirth): array
    {
        $response = Http::get($this->url, [
            'key' => $this->key,
            'type' => $this->type,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'patronymic' => $patronymic,
            'dob' => $dateOfBirth,
            'regionID' => -1,
        ]);

        if ($response->failed() || data_get($response->json(), 'error') || data_get($response->json(), 'done') === 0) {
            $info = [
                'url' => $response->effectiveUri(), // Получение URL запроса
                'response' => $response->body(),   // Тело ответа
                'status_code' => $response->status(), // Код статуса ответа
            ];
            throw new \Exception('FCCP API error : ' . json_encode($info),400);
        }

        return $response->json();
    }

}