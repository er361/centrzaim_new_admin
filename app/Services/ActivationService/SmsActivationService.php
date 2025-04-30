<?php


namespace App\Services\ActivationService;


use App\Models\SmsProvider;
use App\Models\User;
use App\Services\SmsService\SmsServiceContract;
use App\Services\SmsService\SmsServiceFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SmsActivationService implements ActivationServiceInterface
{
    /**
     * @var SmsServiceContract
     */
    protected SmsServiceContract $service;

    /**
     * @var SmsProvider
     */
    protected $smsProvider;

    /**
     * SmsRuActivationService constructor.
     */
    public function __construct()
    {
        $this->smsProvider = SmsProvider::query()
            ->where('is_for_activation', 1)
            ->first();

        $this->service = (new SmsServiceFactory())->getService(
            $this->smsProvider->service_id
        );
    }

    public function sendCode(User $user): void
    {
        if ($user->mphone === null) {
            return;
        }

        if ($user->activation_code === null) {
            $this->generateCodeForUser($user);

            $this->sendMessage($user, $user->activation_code);
        }
    }

    public function resendCode(User $user): void
    {
        if ($user->mphone === null) {
            return;
        }

        if ($user->activation_code === null) {
            $this->generateCodeForUser($user);
        }

        $this->sendMessage($user, $user->activation_code);
    }

    public function validateCode(User $user, string $providedCode): bool
    {
        if ($user->mphone === null) {
            return false;
        }

        if ($providedCode != $user->activation_code) {
            return false;
        }

        $user->is_active = true;
        $user->activation_code = null;
        $user->save();
        return true;
    }

    /**
     * @param User $user
     */
    protected function generateCodeForUser(User $user): void
    {
        $activationCode = rand(100000, 999999);
        $user->activation_code = (string)$activationCode;
        $user->save();
    }

    /**
     * @param User $user
     * @param string $code
     */
    protected function sendMessage(User $user, string $code) {
        if (!$this->shouldSendSms($user)) {
            return;
        }

        $appUrl = preg_replace('#^https?://#', '', rtrim(config('app.url'),'/'));
        $message = "{$appUrl}: {$code}";

        $this->service->send($this->smsProvider, $user->mphone, $message);
        $this->smsSent($user);
    }

    /**
     * Проверка, должны ли отправлять SMS пользователю.
     * @param User $user
     * @return bool
     */
    protected function shouldSendSms(User $user): bool
    {
        return !Cache::has($this->getSmsThrottleCacheKey($user));
    }

    /**
     * Запоминаем информацию об отправке SMS.
     * @param User $user
     */
    protected function smsSent(User $user): void
    {
        $now = Carbon::now();

        Cache::put(
            $this->getSmsThrottleCacheKey($user),
            $now->toDateTimeString(),
            $now->addMinute()
        );
    }

    /**
     * @param User $user
     * @return string
     */
    protected function getSmsThrottleCacheKey(User $user): string
    {
        return 'sms_throttle_user_id_' . $user->id;
    }
}