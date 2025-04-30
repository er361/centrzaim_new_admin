<?php

namespace App\Services\PostbackService;

use App\Models\User;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Log;

class PostbackServiceStepDecider
{
    /**
     * @var string
     */
    public const STEP_ACTIVATION = 'activation';

    /**
     * @var string Полное заполнение анкеты
     */
    public const STEP_FILL = 'fill';

    /**
     * @var string Успешная привязка карты
     */
    public const STEP_PAYMENT = 'payment';

    /**
     * @var string Никогда не отправлять
     */
    public const STEP_NONE = 'none';

    /**
     * @var string[] Все шаги. Порядок для выбора STEP_PAYMENT по умолчанию при рендере в select (при отсутствии настроек).
     */
    public const STEPS = [
        self::STEP_PAYMENT => 'После привязки карты',
        self::STEP_FILL => 'После заполнения анкеты',
        self::STEP_ACTIVATION => 'После подтверждения телефона',
        self::STEP_NONE => 'Никогда',
    ];

    public function getPostbackStep(User $user): string
    {
        $webmasterStep = $user->webmaster?->postback_step ?? null;
        $settingsStep = SettingsService::getPostbackStep();
        $selectedStep = $webmasterStep ?? $settingsStep;

        // Если никогда не отправляем, не делаем никакие дополнительные проверки
        if ($settingsStep === self::STEP_NONE || $webmasterStep === self::STEP_NONE) {
            Log::debug('Никогда не отправляем постбэк по пользователю.', [
                'user_id' => $user->id,
            ]);
            return $selectedStep;
        }

        // Чтобы отправить постбэк после привязки номера, достаточно убедиться, что у нас включено подтверждение аккаунтов по SMS
        if ($selectedStep === self::STEP_ACTIVATION && !SettingsService::isPhoneVerificationEnabled()) {
            return self::STEP_FILL;
        } elseif ($selectedStep === self::STEP_ACTIVATION) {
            return $selectedStep;
        }

        // После заполнения анкеты можем отправить всегда, даже если выключены все шаги
        if ($selectedStep === self::STEP_FILL) {
            return $selectedStep;
        }

        // После привязки карты можем отправить только если:
        // 1. Мы покажем пользователю платежную форму
        // 2. Платежная форма не выключена глобально на сайте
        $willSeePaymentForm = $user->is_payment_required && SettingsService::isPaymentsEnabled();
        if ($selectedStep === self::STEP_PAYMENT && $willSeePaymentForm) {
            return $selectedStep;
        }

        Log::debug('Не покажем пользователю платежную форму, отправляем постбэк после заполнения анкеты.', [
            'user_id' => $user->id,
        ]);

        // Не увидит форму, отправляем постбэк после регистрации
        return self::STEP_FILL;
    }
}