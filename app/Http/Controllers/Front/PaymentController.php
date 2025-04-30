<?php


namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService\Contracts\PaymentServiceInterface;
use App\Services\PaymentService\Contracts\PayUsingForm;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class PaymentController extends Controller
{
    /**
     * Страница привязки карты.
     *
     * @return Application|Factory|RedirectResponse|View
     */
    public function index(): Factory|View|RedirectResponse|Application
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->canCreatePayment($user)) {
            return redirect()->route('vitrina');
        }

        return view('account.payments.index');
    }

    /**
     * Страница формы платежа.
     *
     * @param PaymentServiceInterface $paymentService
     * @param Request $request
     * @return Application|Factory|RedirectResponse|View
     * @throws GuzzleException
     */
    public function form(PaymentServiceInterface $paymentService, Request $request): Factory|View|RedirectResponse|Application
    {
        if (!$paymentService instanceof PayUsingForm) {
            throw new RuntimeException('Данная платежная система не поддерживается.');
        }

        /** @var User $user */
        $user = Auth::user();

        if (!$this->canCreatePayment($user)) {
            return redirect()->route('account.payments.result');
        }

        $planConfiguration = config("payments.plans.{$user->payment_plan}");

        /** @var Payment $payment */
        $payment = $user->payments()->create([
            'service' => $paymentService->getService(),
            'amount' => $planConfiguration['initial_payment'],
            'type' => Payment::TYPE_DEFAULT,
            'status' => Payment::STATUS_CREATED,
            'access_key' => Str::random(Payment::ACCESS_KEY_LENGTH),
        ]);

        $formData = $paymentService->getPaymentFormData($payment);

        return view('account.payments.form', [
            'method' => $formData->method,
            'url' => $formData->url,
            'fields' => $formData->fields,
            'payment' => $payment,
        ]);
    }

    /**
     * Страница успешного платежа.
     *
     * @return Application|Factory|View
     */
    public function result(): Factory|View|Application
    {
        return view('account.payments.result');
    }

    /**
     * Проверка, можно ли показывать пользователю страницу платежа.
     * @param User $user
     * @return bool
     */
    protected function canCreatePayment(User $user): bool
    {
        if ($user->is_disabled) {
            return false;
        }

        if (!$user->is_payment_required) {
            return false;
        }

        // Показываем форму только если нет платежей
        return $user->payments()
            ->whereTypeDefault()
            ->whereIn('status', [
                Payment::STATUS_PAYED,
                Payment::STATUS_CARD_ADDED,
            ])
            ->doesntExist();
    }
}