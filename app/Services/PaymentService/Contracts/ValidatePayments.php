<?php


namespace App\Services\PaymentService\Contracts;


use App\Services\PaymentService\PaymentData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface ValidatePayments
{
    /**
     * Проверяет валидность платежа и обновляет его данные.
     * В случае любых ошибок в платеже (например, некорректная подпись) должна выбрасывать ошибку.
     *
     * @param Request $request
     *
     * @return PaymentData
     */
    public function validatePayment(Request $request): PaymentData;

    /**
     * Получить объект ответа для callback.
     * @return Response
     */
    public function getResponse(): Response;
}