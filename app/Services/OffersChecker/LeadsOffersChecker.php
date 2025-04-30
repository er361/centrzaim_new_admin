<?php

namespace App\Services\OffersChecker;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadsOffersChecker
{
    private const API_URL = 'https://api.leads.su';
    private const API_TOKEN = 'de91b9234bbfd113de2171e70dcd343c';

    private PendingRequest $api;

    public function __construct(
        private array $offers = []
    )
    {
        $this->api = Http::withQueryParameters([
            'token' => self::API_TOKEN,
        ])->withHeaders([
            'Content-Type' => 'application/json',
        ]);

        $this->offers = Settings::$OFFER_IDS;
    }


    /**
     * Отправка запроса для проверки телефонов.
     *
     * @param string $name
     * @param array $phones
     * @param array $offers
     * @return int
     * @throws Exception
     */
    public function checkPhones(string $name, array $phones, array $offers = []): int
    {
        if (empty($offers)) {
            $offers = $this->offers;
        }

        $response = $this->api->post(self::API_URL . '/webmaster/checker/checkPhones', [
            'name' => $name,
            'phones' => $phones,
            'offers' => $offers,
        ]);

        if($response->successful() && $response->json()['success']) {
            return $response->json()['report_id'] ?? 0;
        }

        Log::error('Failed to check phones', $response->json());
        throw new Exception('Failed to check phones');
    }

    /**
     * Получение отчета по ID.
     *
     * @param int $id
     * @return array|null
     * @throws Exception
     */
    public function getReport(int $id): ?array
    {
        $response = $this->api->get(self::API_URL . '/webmaster/checker/getReport', [
            'id' => $id,
            'token' => self::API_TOKEN,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Failed to get report', $response->json());
        throw new \Exception('Failed to get report');

    }
}