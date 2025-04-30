<?php

namespace App\Console\Commands;

use App\Models\Sms;
use App\Models\SmsUser;
use App\Services\SmsService\Exceptions\EmptyResponseException;
use App\Services\SmsService\Exceptions\StatusCheckingNotSupportedException;
use App\Services\SmsService\SmsServiceFactory;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class LoadSmsUserStatus extends Command
{
    /**
     * Время, в течение которого не проверяем статус SMS.
     */
    protected const SMS_GRACE_PERIOD_SECONDS = 60 * 15;

    /**
     * Лимит на получение SMS из базы данных.
     */
    protected const SMS_LIMIT = 20;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:load-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Загружает статусы отправки SMS от внешних провайдеров.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var SmsServiceFactory $factory */
        $factory = app()->make(SmsServiceFactory::class);

        $baseSmsUserQuery = SmsUser::query()
            ->where('status', SmsUser::STATUS_SEND)
            ->whereNotNull('api_id')
            ->where('created_at', '>=', now()->subWeek())
            ->where('created_at', '<=', now()->subSeconds(self::SMS_GRACE_PERIOD_SECONDS));

        $sms = Sms::query()
            ->with('smsProvider')
            ->whereNotNull('sms_provider_id')
            ->get();

        /** @var Sms $singleSms */
        foreach ($sms as $singleSms) {
            if ($singleSms->smsProvider === null) {
                continue;
            }

            $smsService = $factory->getService($singleSms->smsProvider->service_id);

            (clone $baseSmsUserQuery)
                ->where('sms_id', $singleSms->id)
                ->orderBy('sms_id')
                ->orderBy('user_id')
                ->chunk(self::SMS_LIMIT, function (Collection $smsCollection) use ($singleSms, $smsService) {
                    $smsCollection = $smsCollection->keyBy('api_id');
                    $apiIds = $smsCollection->keys();

                    try {
                        $statuses = $smsService->checkStatus($singleSms->smsProvider, $apiIds->toArray());

                        foreach ($statuses as $apiId => $status) {
                            $this->processStatus($smsCollection, $apiId, $status);
                        }
                    } catch (StatusCheckingNotSupportedException $exception) {
                        return;
                    } catch (EmptyResponseException $e) {
                        $this->warn('Получили пустой ответ...');

                        // Пытаемся понять, какие api_id проблемные, проверяя по одному
                        foreach ($apiIds as $apiId) {
                            try {
                                $statuses = $smsService->checkStatus($singleSms->smsProvider, [$apiId]);

                                foreach ($statuses as $apiId => $status) {
                                    $this->processStatus($smsCollection, $apiId, $status);
                                }
                            } catch (EmptyResponseException $e) {
                                $this->processStatus($smsCollection, $apiId, SmsUser::STATUS_EXPIRED);
                            }
                        }
                    } catch (Throwable $e) {
                        $this->error($e->getMessage());
                        report($e);
                        return;
                    }
                });
        }
    }

    /**
     * Обрабатывает статус
     * @param Collection $smsCollection
     * @param string $apiId
     * @param int $status
     * @return void
     */
    protected function processStatus(Collection $smsCollection, string $apiId, int $status): void
    {
        /** @var SmsUser|null $sms */
        $sms = $smsCollection->get($apiId);

        if ($sms === null) {
            $this->warn('Не нашли SMS по api_id.');
            return;
        }

        // @todo Исправить Pivot
        SmsUser::query()
            ->where('sms_id', $sms->sms_id)
            ->where('user_id', $sms->user_id)
            ->where('api_id', $sms->api_id)
            ->update([
                'status' => $status,
            ]);
    }
}
