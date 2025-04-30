<?php

namespace App\Services\SmsService;

use App\Models\Showcase;
use App\Models\Sms;
use App\Models\SmsUser;
use App\Models\User;
use App\Services\LinkShortenService\CuttLyLinkShortener;
use App\Services\LinkShortenService\LinkShortenServiceContract;
use App\Services\LinkShortenService\NullLinkShortener;
use App\Services\SmsService\Exceptions\InvalidRecipientException;
use App\Services\SmsService\Exceptions\SmsSenderException;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use MagicLink\Actions\LoginAction;
use MagicLink\MagicLink;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SmsSender
{
    /**
     * @var LinkShortenServiceContract
     */
    protected LinkShortenServiceContract $defaultLinkShortener;

    /**
     * @var LinkShortenServiceContract
     */
    protected LinkShortenServiceContract $nullLinkShortener;

    /**
     * SmsSender constructor.
     */
    public function __construct()
    {
        $this->defaultLinkShortener = app(LinkShortenServiceContract::class);
        $this->nullLinkShortener = new NullLinkShortener();
    }

    /**
     * Отправить SMS со ссылкой на внешний сервис.
     * @param Sms $sms
     * @param User $user
     * @return void
     * @throws Throwable
     */
    public function sendExternalLinkSms(Sms $sms, User $user): void
    {
        $link = route('front.sms.redirect', [
            'sms' => $sms,
            'user_id' => $user->id,
            'key' => $sms->getSecretKey((string)$user->id),
        ]);

        $this->processSms($sms, $user, $link, $this->defaultLinkShortener);
    }

    /**
     * Отправить SMS со ссылкой на форму входа.
     * @param Sms $sms
     * @param User $user
     * @return void
     * @throws Throwable
     */
    public function sendLoginLinkSms(Sms $sms, User $user): void
    {
        $action = new LoginAction($user);
        $action->response(redirect()->route('account.payments.index', [
            'force' => 1
        ]));

        $link = MagicLink::create($action)->url;

        $this->processSms($sms, $user, $link, $this->defaultLinkShortener);
    }

    /**
     * Отправить SMS со ссылкой на стороннюю витрину.
     * @param Sms $sms
     * @param User $user
     * @return void
     * @throws Throwable
     */
    public function sendExternalShowcaseSms(Sms $sms, User $user): void
    {
        $body = [
            'base_url' => URL::to('/'),
            'query' => [
                'showcase_id' => $sms->showcase_id,
                'sms_id' => $sms->id,
                'webmaster_id' => $user->webmaster?->id,
                'user_id' => $user->id,
            ]
        ];

        /** @var Showcase $showcase */
        $showcase = Showcase::query()->find($sms->showcase_id);
        $url = $showcase->external_url . '/api/v1/links';
        $link = Http::withHeaders([
            'X-TOKEN' => config('services.showcases.token'),
        ])
            ->post($url, $body)
            ->throw()
            ->json('data.link');

        $this->processSms($sms, $user, $link, $this->nullLinkShortener);
    }

    /**
     * Обрабатывает SMS и возможные ошибки при отправке.
     * @param Sms $sms
     * @param User $user
     * @param string $link
     * @param LinkShortenServiceContract $linkShortenerService
     * @return void
     * @throws RequestException
     * @throws Throwable
     */
    protected function processSms(Sms $sms, User $user, string $link, LinkShortenServiceContract $linkShortenerService): void
    {
        try {
            $apiId = $this->sendSms($sms, $user, $link, $linkShortenerService);

            SmsUser::query()
                ->create([
                    'status' => SmsUser::STATUS_SEND,
                    'sms_id' => $sms->id,
                    'user_id' => $user->id,
                    'api_id' => $apiId,
                    'cost' => $sms->smsProvider->sms_cost,
                    'service_id' => 0, // @todo Удалить колонку
                ]);
        } catch (InvalidRecipientException $e) {
            SmsUser::query()
                ->create([
                    'status' => SmsUser::STATUS_SENDING_FAILED,
                    'sms_id' => $sms->id,
                    'user_id' => $user->id,
                    'api_id' => null,
                    'cost' => 0,
                    'service_id' => 0, // @todo Удалить колонку
                    'error' => $e->getMessage(),
                ]);
        } catch (RequestException $e) {
            if ($e->response->status() !== Response::HTTP_TOO_MANY_REQUESTS) {
                report($e);
            }

            throw new SmsSenderException('', 0, $e);
        } catch (Throwable $e) {
            report($e);

            throw new SmsSenderException('', 0, $e);
        }
    }

    /**
     * @param Sms $sms
     * @param User $user
     * @param string $link
     * @param LinkShortenServiceContract $linkShortenerService
     * @return string|null
     * @throws Exception
     */
    protected function sendSms(Sms $sms, User $user, string $link, LinkShortenServiceContract $linkShortenerService): ?string
    {
        $link = retry(3, function () use ($link, $linkShortenerService) {
            return $linkShortenerService->get($link);
        }, $linkShortenerService->getDelay());

        $message = $sms->text;
        $message = str_replace(Sms::LINK_TEMPLATE, $link, $message);
        $message = str_replace(Sms::NAME_TEMPLATE, $user->first_name ?? '', $message);


        $smsService = (new SmsServiceFactory())
            ->getService(
                $sms->smsProvider->service_id
            );

        $apiId = $smsService->send(
            $sms->smsProvider,
            $user->mphone,
            $message,
            $sms->from
        );

        sleep($linkShortenerService->getDelay());

        return $apiId;
    }
}