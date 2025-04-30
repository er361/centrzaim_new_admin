<?php


namespace App\Http\Controllers\Api\V1;

use App\Events\UserPaymentSuccessful;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService\Contracts\PaymentServiceInterface;
use App\Services\PaymentService\Contracts\ValidatePayments;
use App\Services\PaymentService\PaymentData;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PaymentController extends Controller
{
    /**
     * Получение информации о платеже.
     *
     * @param Payment $payment
     * @param Request $request
     *
     * @return PaymentResource
     */
    public function show(Payment $payment, Request $request): PaymentResource
    {
        // В случае, если не совпадает уникальный ключ для проверки платежа,
        // или создан более часа назад, запрещаем доступ.

        if ($request->input('access_key') !== $payment->access_key) {
            abort(401);
        }

        if ($payment->created_at->lt(now()->subHour())) {
            abort(400);
        }

        return PaymentResource::make($payment);
    }

    /**
     * Обработка платежа.
     *
     * @param string $method
     * @param Request $request
     *
     * @return Response
     * @throws BindingResolutionException
     */
    public function process(string $method, Request $request)
    {
        $logger = Log::channel('payments');

        $logger->debug('Получен запрос на обработку платежа.', [
            'method' => $method,
            'request' => $request->all(),
        ]);

        // @todo Не проверяем параметр method

        $service = app()->make(PaymentServiceInterface::class);

        if (!$service instanceof ValidatePayments) {
            throw new RuntimeException('Данная платежная система не поддерживает callback.');
        }

        try {
            $paymentData = $service->validatePayment($request);
        } catch (Throwable $e) {
            report($e);
            abort(400);
        }

        /** @var $paymentData PaymentData */

        // Если человек попал сюда, значит платежи включены
        // Отправляем постбэк
        if (in_array($paymentData->payment->status, [Payment::STATUS_PAYED, Payment::STATUS_CARD_ADDED])) {
            event(new UserPaymentSuccessful($paymentData->user));
        }

        return $service->getResponse();
    }
}