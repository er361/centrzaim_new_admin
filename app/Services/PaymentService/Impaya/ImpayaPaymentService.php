<?php


namespace App\Services\PaymentService\Impaya;


use App\Models\Payment;
use App\Services\PaymentService\Contracts\PaymentServiceInterface;
use App\Services\PaymentService\Contracts\PayRecurrent;
use App\Services\PaymentService\Contracts\PayUsingForm;
use App\Services\PaymentService\Contracts\ValidatePayments;
use App\Services\PaymentService\Exceptions\ValidationException;
use App\Services\PaymentService\FormData;
use App\Services\PaymentService\Impaya\Request\Card;
use App\Services\PaymentService\Impaya\Request\Credentials;
use App\Services\PaymentService\Impaya\Request\Good;
use App\Services\PaymentService\Impaya\Request\InitRequest;
use App\Services\PaymentService\Impaya\Request\PaymentNotification;
use App\Services\PaymentService\Impaya\Request\PayRequest;
use App\Services\PaymentService\PaymentData;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class ImpayaPaymentService implements PaymentServiceInterface, PayUsingForm, ValidatePayments, PayRecurrent
{
    /**
     * @var string
     */
    protected string $terminalName;

    /**
     * @var string
     */
    protected string $recurrentTerminalName;

    /**
     * @var string
     */
    protected string $recurrentOldTerminalName;

    /**
     * @var string
     */
    protected string $terminalPassword;

    /**
     * @var string
     */
    protected string $merchantName;

    /**
     * @var string
     */
    protected string $merchantPassword;

    /**
     * @var ImpayaApiClient
     */
    protected ImpayaApiClient $client;

    /**
     * @var string
     */
    protected string $baseUrl;

    /**
     * ImpayaPaymentService constructor.
     */
    public function __construct()
    {
        // Определяем текущее окружение
        $env = config('services.impaya.env'); // Теперь это строка, а не Closure

        // Получаем конфигурацию для нужного окружения
        $impayaConfig = config("services.impaya.{$env}");

        $this->terminalName = $impayaConfig['terminal_name'];
        $this->recurrentTerminalName = $impayaConfig['recurrent_terminal_name'];
        $this->recurrentOldTerminalName = $this->recurrentTerminalName . '_old'; // Выданные терминалы с суффиксом _old
        $this->terminalPassword = $impayaConfig['terminal_password'];
        $this->merchantName = $impayaConfig['merchant_name'];
        $this->merchantPassword = $impayaConfig['merchant_password'];
        $this->baseUrl = $impayaConfig['base_url'];
        $this->client = new ImpayaApiClient($this->baseUrl);
    }

    /**
     * Получить данные для генерации формы платежа.
     *
     * @param  Payment  $payment
     *
     * @return FormData
     * @throws GuzzleException
     */
    public function getPaymentFormData(Payment $payment): FormData
    {
        $request = new InitRequest([
            'key' => $this->terminalName,
            'merchant_order_id' => (string)$payment->id,
            'amount' => $payment->amount * 100, // Переводим в копейки
            'add_card' => true,
            'type' => InitRequest::TYPE_PAY,
            'payment_type' => InitRequest::PAYMENT_TYPE_TWO_STEP,
            'action' => InitRequest::ACTION_UNBLOCK,
            'recurrent' => true,
            'credential' => $this->getCredentials(),
            'custom_params_raw' => $this->getCustomParams($payment),
            'goods' => $this->getGoods($payment),
        ]);

        $initResponse = $this->client->init($request);

        if (!$initResponse->Success) {
            throw new RuntimeException($initResponse->ErrCode);
        }

        $payment->update([
            'status' => Payment::STATUS_ADD_CARD_CREATED,
        ]);

        return new FormData([
            'url' => "{$this->baseUrl}/createPayment",
            'method' => Request::METHOD_POST,
            'fields' => [
                'sessionId' => $initResponse->SessionGUID,
            ]
        ]);
    }

    /**
     * Получить сервис платежа для сохранения в Payments.
     * @return int
     */
    public function getService(): int
    {
        return Payment::SERVICE_IMPAYA;
    }

    /**
     * Проверяет валидность платежа и обновляет его данные.
     * В случае любых ошибок в платеже (например, некорректная подпись) должна выбрасывать ошибку.
     *
     * @param  Request  $request
     *
     * @return PaymentData
     * @throws UnknownProperties
     */
    public function validatePayment(Request $request): PaymentData
    {
        $requestData = $request->all();
        $requestData['Amount'] = (int)$requestData['Amount'];
        $requestData['Success'] = $requestData['Success'] === 'true';
        $notification = new PaymentNotification($requestData);

        $this->checkSignature($notification);

        /** @var Payment|null $payment */
        $payment = Payment::query()
            ->where('id', $notification->OriginalOrderId)
            ->first();

        if ($payment === null) {
            throw new ValidationException('Платеж не найден.');
        }

        $failedNotificationTypes = ['Block', 'Pay', 'Block3DS'];

        $newStatus = $payment->status;
        $commission = $payment->commission;
        $errorCode = $payment->error_code;
        $cardNumber = $payment->card_number;

        // @todo Переделать логику движения по статусам
        // Если платеж уже оплачен, не пересчитываем комиссию и новый статус
        // Не прерываем обработку выше, чтобы не потерять rebill_id
        if (!in_array($payment->status, [Payment::STATUS_PAYED, Payment::STATUS_CARD_ADDED])) {
            $errorCode ??= $notification->ErrCode;

            $isSuccess = ($notification->Success && $notification->Notification === 'Charge')
                || ($notification->Success && $notification->Notification === 'Pay' && $notification->State === 'Charged');

            // Если создан запрос на привязку карты, то комиссия не списывается,
            // и нам достаточно успешной блокировки, чтобы ответа по Block
            if ($payment->status == Payment::STATUS_ADD_CARD_CREATED
                && $notification->Success
                && $notification->Notification === 'AddCard') {
                $newStatus = Payment::STATUS_CARD_ADDED;
                $commission = 0; // За привязку карты не списывается комиссия
            } elseif ($isSuccess) {
                $newStatus = Payment::STATUS_PAYED;
                $commission = $this->getCommission($payment);
            } elseif (!$notification->Success && in_array($notification->Notification, $failedNotificationTypes)) {
                $newStatus = Payment::STATUS_DECLINED;
                $commission = config('services.impaya.commission.default_unsuccessful');
            }
        }

        if (!empty($notification->CardNumber)) {
            $cardNumber = $notification->CardNumber;
        }

        $payment->update([
            'status' => $newStatus,
            'rebill_id' => $notification->CardUId ?? $payment->rebill_id,
            'commission' => $commission,
            'error_code' => $errorCode,
            'card_number' => $cardNumber,
        ]);

        return new PaymentData([
            'user' => $payment->user,
            'payment' => $payment,
        ]);
    }

    /**
     * Получить объект ответа для callback.
     * @return Response
     */
    public function getResponse(): Response
    {
        return response('OK');
    }

    /**
     * Совершить рекуррентный платеж.
     *
     * @param  Payment  $currentPayment  Текущий платеж, который необходимо провести
     * @param  Payment  $defaultPayment  Платеж на привязку карты
     */
    public function initRecurrent(Payment $currentPayment, Payment $defaultPayment): void
    {
        $logger = Log::channel('payments');

        // Все платежи до 23 апреля нужно списывать через старый терминал
        $key = $defaultPayment->created_at->lte('2024-04-23 00:00:00')
            ? $this->recurrentOldTerminalName
            : $this->recurrentTerminalName;

        try {
            $card = new Card([
                'uid' => $defaultPayment->rebill_id,
            ]);

            $request = new PayRequest([
                'key' => $key,
                'card' => $card,
                'merchant_order_id' => (string)$currentPayment->id,
                'amount' => $currentPayment->amount * 100, // Переводим в копейки
                'credential' => $this->getCredentials(false),
                'custom_params' => 'Email='.$currentPayment->user->email,
                'goods' => $this->getGoods($currentPayment),
            ]);

            $response = $this->client->pay($request);

            if (!$response->Success) {
                $logger->debug('Рекуррентный платеж отклонен.', [
                    'id' => $currentPayment->id,
                    'code' => $response->ErrCode,
                    'message' => $response->ErrMessage,
                ]);
                $commission = config('services.impaya.commission.default_unsuccessful');
            } else {
                $logger->debug('Рекуррентный платеж успешен.', [
                    'id' => $currentPayment->id,
                    'response' => json_encode($response),
                ]);
                $commission = $this->getCommission($currentPayment);
            }

            $currentPayment->update([
                'status' => $response->Success
                    ? Payment::STATUS_PAYED
                    : Payment::STATUS_DECLINED,
                'commission' => $commission,
            ]);

            if ($response->Success) {
                $currentPayment->user->update([
                    'recurrent_payment_consequent_error_count' => 0,
                    // Сбрасываем счетчик ошибок
                    'recurrent_payment_success_count' => $currentPayment->user->recurrent_payment_success_count + 1,
                    // Увеличиваем счетчик успешных платежей
                ]);
            } else {
                $currentPayment->user->update([
                    'recurrent_payment_consequent_error_count' => $currentPayment->user->recurrent_payment_consequent_error_count + 1,
                    // Увеличиваем счетчик ошибок
                    // Не увеличиваем счетчик успешных платежей, т.к. платеж не прошел
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Неизвестная ошибка при обработке рекуррентного платежа.', [
                'id' => $currentPayment->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            report($e);
            $currentPayment->update([
                'status' => Payment::STATUS_DECLINED,
            ]);
            $currentPayment->user->update([
                'recurrent_payment_consequent_error_count' => $currentPayment->user->recurrent_payment_consequent_error_count + 1,
                // Увеличиваем счетчик ошибок
                // Не увеличиваем счетчик успешных платежей, т.к. платеж не прошел
            ]);
        }
    }

    /**
     * @param  PaymentNotification  $paymentNotification
     */
    protected function checkSignature(PaymentNotification $paymentNotification): void
    {
        // Выключено в связи с тем, что платежная система не передает подпись
    }

    /**
     * Получить дополнительные параметры платежа для отображения на шаблоне.
     *
     * @param  Payment  $payment
     * @return string
     */
    protected function getCustomParams(Payment $payment): string
    {
        $customParamsData = [
            'successUrl' => route('account.payments.result'),
            'failUrl' => route('account.payments.result'),
//            'Description' => 'Привязать карту для удачной верификации вашего аккаунта',
            'PayButtonCustomText' => 'Привязать карту',
            'Email' => $payment->user->email,
        ];

        $customParamStrings = [];

        foreach ($customParamsData as $name => $value) {
            $customParamStrings[] = "{$name}={$value}";
        }

        return implode(';', $customParamStrings);
    }

    /**
     * @param  bool  $shouldIncludeTerminalPassword
     * @return Credentials
     * @throws UnknownProperties
     */
    protected function getCredentials(bool $shouldIncludeTerminalPassword = true): Credentials
    {
        $data = [
            'merchant_name' => $this->merchantName,
            'merchant_password' => $this->merchantPassword,
        ];

        if ($shouldIncludeTerminalPassword) {
            $data['terminal_password'] = $this->terminalPassword;
        }

        return new Credentials($data);
    }

    /**
     * Получить список товаров.
     * @param  Payment  $payment
     * @return array
     */
    protected function getGoods(Payment $payment): array
    {
        return [
            new Good([
                'name' => 'Услуга по подбору займа',
                'price' => (string)$payment->amount * 100, // Переводим в копейки
                'tax' => 6, // Ставка налога, уточнена у бухгалтера
            ])
        ];
    }

    /**
     * @param  Payment  $payment
     * @return float
     */
    protected function getCommission(Payment $payment): float
    {
        $percentCommission = config('services.impaya.commission.default_successful') * $payment->amount;
        $minimumCommission = config('services.impaya.commission.min_successful');
        return max($percentCommission, $minimumCommission);
    }
}