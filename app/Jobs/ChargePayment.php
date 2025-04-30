<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService\Contracts\PaymentServiceInterface;
use App\Services\PaymentService\Contracts\PayRecurrent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class ChargePayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Таймаут задачи. Вынесен в константу для использования другими классами.
     */
    public const TIMEOUT = 45;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = self::TIMEOUT;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 1;

    /**
     * @var int Сумма платежа
     */
    public int $amount;

    /**
     * @var User Пользователь для списания денег
     */
    protected User $user;
    private int $subtype;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $amount, User $user, int $subtype)
    {
        $this->amount = $amount;
        $this->user = $user;
        $this->subtype = $subtype;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        /** @var PaymentServiceInterface $service */
        $service = app()->make(PaymentServiceInterface::class);

        if (!$service instanceof PayRecurrent) {
            throw new RuntimeException('Сервис оплаты не поддерживает рекуррентные платежи.');
        }

        $logger = Log::channel('payments');

        /** @var Payment $payment */
        $payment = $this->user->payments()
            ->create([
                'service' => $service->getService(),
                'amount' => $this->amount,
                'type' => Payment::TYPE_RECURRENT,
                'status' => Payment::STATUS_CREATED,
                'subtype' => $this->subtype,
                'payment_number' => 0,
                'iteration_number' => 0,
            ]);
        $payment->setRelation('user', $this->user);

        /** @var Payment $defaultPayment */
        $defaultPayment = $this->user->payments()
            ->whereTypeDefault()
            ->whereStatusInSuccess()
            ->whereNotNull('rebill_id')
            ->orderByDesc('created_at')
            ->first();

        $logger->debug('Начинаем списание платежа пользователя.', [
            'payment_id' => $payment->id,
            'user_id' => $this->user->id,
        ]);

        try {
            $service->initRecurrent($payment, $defaultPayment);
            $logger->debug('Списание платежа завершено.', [
                'payment_id' => $payment->id,
            ]);
        } catch (Throwable $e) {
            report($e);
            $logger->debug('Ошибка при списании платежа', [
                'payment_id' => $payment->id,
            ]);
        }
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
