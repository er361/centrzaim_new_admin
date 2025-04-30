<?php


namespace App\Services\SmsService;


use App\Models\SmsProvider;
use App\Models\SmsUser;
use App\Services\SmsService\Exceptions\EmptyResponseException;
use App\Services\SmsService\Exceptions\InvalidRecipientException;
use App\Services\SmsService\Exceptions\SmsNotSentException;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

class MySmppService implements SmsServiceContract
{
    /**
     * Текст сообщения при успешной отправке сообщения.
     */
    protected const INFORMATION_SEND = 'send';

    /**
     * Текст сообщения при наличии номера телефона в стоп-листе.
     */
    protected const INFORMATION_STOP_LIST = 'Номер телефона присутствует в стоп-листе.';

    /**
     * Текст сообщения при невалидном (для отправки) номере получателя.
     */
    protected const INFORMATION_INVALID_RECIPIENT = 'Данное направление закрыто для вас.';

    /**
     * @var string
     */
    protected string $baseUrl;

    /**
     * @var string
     */
    protected string $login;

    /**
     * @var string
     */
    protected string $password;

    /**
     * @var null|string
     */
    protected ?string $sender;

    /**
     * MySmppService constructor.
     */
    public function __construct()
    {
        $this->baseUrl = config('services.my_smpp.base_url');
    }

    /**
     * Отправить SMS пользователю.
     * @param SmsProvider $smsProvider
     * @param string $phone
     * @param string $message
     * @param string|null $from
     * @return null|string Внешний идентификатор SMS или null, если не поддерживается
     * @throws Exception
     */
    public function send(SmsProvider $smsProvider, string $phone, string $message, string $from = null): ?string
    {
        $this->setCredentials($smsProvider);

        $request = $this->getSendXmlRequest([$phone], $message);
        $response = $this->sendRequest('', $request);
        return $this->getRemoteSmsId($response);
    }

    /**
     * Проверить статус отправки SMS.
     * @param string[] $apiIds Список внешних идентификаторов SMS
     * @return int[] Список статусов, где ключ - внешний идентификатор SMS, а значение - статус
     * @throws Exception
     */
    public function checkStatus(SmsProvider $smsProvider, array $apiIds): array
    {
        $this->setCredentials($smsProvider);

        $request = $this->getCheckXmlRequest($apiIds);
        $response = $this->sendRequest('state.php', $request);

        if (empty($response)) {
            throw new EmptyResponseException();
        }

        return $this->getRemoteStatuses($response);
    }

    /**
     * @param SmsProvider $smsProvider
     * @return void
     */
    protected function setCredentials(SmsProvider $smsProvider): void
    {
        $this->login = $smsProvider->api_login;
        $this->password = $smsProvider->api_password;
        $this->sender = $smsProvider->sender;
    }

    /**
     * Отправить запрос.
     * @param string $method
     * @param string $request
     * @return string
     */
    protected function sendRequest(string $method, string $request): string
    {
        $logger = Log::channel('sms');

        $logger->debug('Запрос к сервису MySmpp: ' . $request);
        $href = $this->baseUrl . '/xml/' . $method;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_URL, $href);
        $response = curl_exec($ch);
        curl_close($ch);

        $logger->debug('Результаты отправки SMS от MySmpp.', [
            'request' => Str::replace(["\r", "\n"], '', $request),
            'response' => Str::replace(["\r", "\n"], '', $response),
        ]);

        return $response;
    }

    /**
     * @param array $phones
     * @param string $messageText
     * @return bool|string
     */
    protected function getSendXmlRequest(array $phones, string $messageText)
    {
        $request = new SimpleXMLElement('<request/>');

        $message = $request->addChild('message');
        $message->addAttribute('type_send_1', 'sms');
        $message->addAttribute('type', 'sms');

        if ($this->sender !== null) {
            $message->addChild('sender', $this->sender);
        }

        $message->addChild('text', $messageText);

        for ($i = 0; $i < count($phones); $i++) {
            $abonent = $message->addChild('abonent');
            $abonent->addAttribute('phone', $phones[$i]);
            $abonent->addAttribute('number_sms', (string)($i + 1));
        }

        $security = $request->addChild('security');

        $login = $security->addChild('login');
        $login->addAttribute('value', $this->login);

        $password = $security->addChild('password');
        $password->addAttribute('value', $this->password);

        return $request->asXML();
    }

    /**
     * @param array $apiIds
     * @return bool|string
     */
    protected function getCheckXmlRequest(array $apiIds)
    {
        $request = new SimpleXMLElement('<request/>');

        $getState = $request->addChild('get_state');

        foreach ($apiIds as $apiId) {
            $getState->addChild('id_sms', $apiId);
        }

        $security = $request->addChild('security');

        $login = $security->addChild('login');
        $login->addAttribute('value', $this->login);

        $password = $security->addChild('password');
        $password->addAttribute('value', $this->password);

        return $request->asXML();
    }

    /**
     * @param string $responseXml
     * @return string
     * @throws Exception
     */
    protected function getRemoteSmsId(string $responseXml): string
    {
        $xml = new SimpleXMLElement($responseXml);
        $information = $xml->information;

        $sendStatus = (string)$information;

        if ($sendStatus === self::INFORMATION_SEND) {
            return (string)$information->attributes()->id_sms;
        }

        $invalidRecipientErrors = [
            self::INFORMATION_INVALID_RECIPIENT,
            self::INFORMATION_STOP_LIST,
        ];

        if (in_array($sendStatus, $invalidRecipientErrors, true)) {
            throw new InvalidRecipientException($information);
        }

        throw new SmsNotSentException($information);
    }

    /**
     * @param string $responseXml
     * @return int[]
     * @throws Exception
     */
    protected function getRemoteStatuses(string $responseXml): array {
        try {
            $xml = new SimpleXMLElement($responseXml);
        } catch (Throwable $e) {
            Log::error('Ошибка при парсинге ответа MySMPP.', [
                'response' =>  $responseXml,
                'exception' => $e,
            ]);
            throw $e;
        }

        $statusMapping = [
            'send' => SmsUser::STATUS_SEND,
            'not_deliver' => SmsUser::STATUS_FAILED,
            'expired' => SmsUser::STATUS_NOT_KNOWN,
            'deliver' => SmsUser::STATUS_DELIVERED,
            'read' => SmsUser::STATUS_DELIVERED,
            'partly_deliver' => SmsUser::STATUS_NOT_KNOWN,
        ];

        $statuses = [];

        foreach ($xml->state as $child) {
            $innerStatus = Arr::get($statusMapping, (string)$child, SmsUser::STATUS_NOT_KNOWN);
            Arr::set($statuses, (string)$child->attributes()->id_sms, $innerStatus);
        }

        return $statuses;
    }
}
