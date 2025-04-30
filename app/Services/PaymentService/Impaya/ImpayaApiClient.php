<?php


namespace App\Services\PaymentService\Impaya;


use App\Services\PaymentService\Impaya\Request\InitRequest;
use App\Services\PaymentService\Impaya\Request\PayRequest;
use App\Services\PaymentService\Impaya\Response\InitResponse;
use App\Services\PaymentService\Impaya\Response\PayResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class ImpayaApiClient
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * ImpayaApiClient constructor.
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        $this->client = new Client([
            'base_uri' => $baseUrl,
        ]);
    }

    /**
     * @param InitRequest $request
     * @return InitResponse
     * @throws GuzzleException
     */
    public function init(InitRequest $request): InitResponse
    {
        Log::channel('payments')->debug('Запрос на инициализацию платежа через Impaya.', [
            'request' => json_encode($request->toArray())
        ]);

        $response = $this->client->post('init', [
            RequestOptions::JSON => $request->toArray(),
        ]);

        $content = $response->getBody()->getContents();

        Log::channel('payments')->debug('Ответ на инициализацию платежа через Impaya.', [
            'response' => $content
        ]);

        $content = json_decode($content, true);

        return new InitResponse($content);
    }

    /**
     * @param PayRequest $request
     * @return PayResponse
     * @throws GuzzleException
     */
    public function pay(PayRequest $request): PayResponse
    {
        Log::channel('payments')->debug('Запрос на оплату через Impaya.', [
            'request' => json_encode($request->toArray())
        ]);

        $response = $this->client->post('pay', [
            RequestOptions::JSON => $request->toArray(),
        ]);

        $content = $response->getBody()->getContents();

        Log::channel('payments')->debug('Ответ на оплату через Impaya.', [
            'response' => $content
        ]);

        $content = json_decode($content, true);

        return new PayResponse($content);
    }
}