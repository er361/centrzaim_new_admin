<?php

namespace App\Console\Commands;

use App\Builders\PaymentBuilder;
use App\Enums\SmsTypeEnum;
use App\Models\Payment;
use App\Models\Sms;
use App\Models\User;
use App\Services\SmsService\Exceptions\SmsSenderException;
use App\Services\SmsService\SmsSender;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendNoCardSms extends Command
{
    /**
     * Размер чанка для выборки SMS из базы данных.
     */
    protected const SMS_CHUNK_SIZE = 50;

    /**
     * Чанк для выборки пользователей.
     */
    protected const USERS_CHUNK_SIZE = 20;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:no-card-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправляет SMS сообщения зарегистрированным пользователям, которые не привязали карту.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('Начинаем отправку SMS пользователям без карт.');

        $smsSender = new SmsSender();

        Sms::query()
            ->where('type',  SmsTypeEnum::NoCard)
            ->where('is_enabled', 1)
            ->whereHas('smsProvider')
            ->with('smsProvider')
            ->with('source')
            ->each(
                function (Sms $sms) use ($smsSender) {
                    $this->info("Обрабатываем SMS #{$sms->id}");

                    User::query()
                        ->whereShouldReceiveSms($sms)
                        // Фильтр по зарегистрированным, но не привязавшим карту
                        ->where('fill_status', User::FILL_STATUS_FINISHED)
                        ->where('is_payment_required', 1)
                        ->where(function (Builder $query) {
                            $query
                                ->whereHas('payments', function (PaymentBuilder $query) {
                                    $query->whereIn('status', [
                                        Payment::STATUS_CREATED,
                                        Payment::STATUS_ADD_CARD_CREATED,
                                    ]);
                                })
                                ->orWhereDoesntHave('payments');
                        })
                        ->eachById(function (User $user) use ($sms, $smsSender) {
                            $this->info("Отправляем SMS пользователю #{$user->id}...");

                            try {
                                $smsSender->sendLoginLinkSms($sms, $user);
                                $this->info("Отправили SMS пользователю #{$user->id}");
                            } catch (SmsSenderException $e) {
                                Log::warning("Ошибка SmsSenderException при отправке SMS без карт {$sms->id} пользователю {$user->id}: {$e->getMessage()}");
                            } catch (Throwable $e) {
                                Log::warning("Ошибка при отправке SMS без карт {$sms->id} пользователю {$user->id}: {$e->getMessage()}");
                                report($e);
                            }
                        }, self::USERS_CHUNK_SIZE);
                },
                self::SMS_CHUNK_SIZE
            );

        Log::info('Завершили отправку SMS пользователям без карт.');
    }
}
