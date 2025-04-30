<?php

namespace App\Console\Commands;

use App\Builders\UserBuilder;
use App\Enums\SmsTypeEnum;
use App\Models\Sms;
use App\Models\User;
use App\Services\SmsService\Exceptions\SmsSenderException;
use App\Services\SmsService\SmsSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendDefaultSms extends Command
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
    protected $signature = 'sms:default-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправляет SMS сообщения зарегистрированным пользователям.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::channel('commands')->info('Начинаем отправку SMS зарегистрированным пользователям.');

        $smsSender = new SmsSender();

        Sms::query()
            ->whereIn('type', [SmsTypeEnum::Default, SmsTypeEnum::AfterClick])
            ->where('is_enabled', 1)
            ->whereHas('smsProvider')
            ->with('smsProvider')
            ->with('source')
            ->each(
                function (Sms $sms) use ($smsSender) {
                    $this->info("Обрабатываем SMS #{$sms->id}");

                    User::query()
                        ->whereShouldReceiveSms($sms)
                        ->when($sms->source_id !== null, function (UserBuilder $query) use ($sms) {
                            $query->whereSourceId($sms->source_id);
                        })
                        ->eachById(function (User $user) use ($sms, $smsSender) {
                            $this->info("Отправляем SMS пользователю #{$user->id}...");
                            try {
                                if ($sms->showcase_id === null) {
                                    $smsSender->sendExternalLinkSms($sms, $user);
                                } else {
                                    $smsSender->sendExternalShowcaseSms($sms, $user);
                                }

                                $this->info("Отправили SMS пользователю #{$user->id}");
                            } catch (SmsSenderException $e) {
                                Log::channel('commands')->warning("Ошибка SmsSenderException при отправке SMS {$sms->id} пользователю {$user->id}: {$e->getMessage()}");
                            } catch (Throwable $e) {
                                Log::channel('commands')->warning("Ошибка при отправке SMS {$sms->id} пользователю {$user->id}: {$e->getMessage()}");
                                report($e);
                            }
                        }, self::USERS_CHUNK_SIZE);
                },
                self::SMS_CHUNK_SIZE
            );

        Log::channel('commands')->info('Завершили отправку SMS зарегистрированным пользователям.');
    }
}
