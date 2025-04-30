<?php

namespace App\Services\SettingsService;


use App\Services\PostbackService\PostbackServiceStepDecider;
use App\Services\SettingsService\Enums\FrontendSettingsEnum;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SiteService;

/**
 * @todo Сделать не static все методы
 */
class SettingsService
{
    /**
     * Проверка, включены ли платежи.
     *
     * @return bool
     */
    public static function isPaymentsEnabled(): bool
    {
        return setting('is_payments_enabled') === '1';
    }

    /**
     * Проверка, включено ли подтверждение телефона.
     *
     * @return bool
     */
    public static function isPhoneVerificationEnabled(): bool
    {
        return setting('is_phone_verification_enabled') === '1';
    }

    /**
     * Получить шаг для отправки постбэка.
     * @return string
     * @see PostbackServiceStepDecider
     */
    public static function getPostbackStep(): string
    {
        return setting('postback_step', PostbackServiceStepDecider::STEP_PAYMENT);
    }

    /**
     * Проверка, включено ли заполнение первого шага аккаунта.
     *
     * @return bool
     */
    public static function isAccountFillStep1Enabled(): bool
    {
        return setting('is_account_fill_step_1_enabled') === '1';
    }

    /**
     * Проверка, включено ли заполнение второго шага аккаунта.
     *
     * @return bool
     */
    public static function isAccountFillStep2Enabled(): bool
    {
        return setting('is_account_fill_step_2_enabled') === '1';
    }

    /**
     * Нужно ли отправлять пользователей из партнерских программ сразу
     * на страницу регистрации (вместо главной).
     * @return bool
     */
    public function shouldRedirectToRegisterPageFromSources(): bool
    {
        return setting('should_redirect_to_register_page_from_sources') === '1';
    }

    /**
     * Получить список включенных полей.
     * @return array
     */
    public static function getEnabledFillSteps(): array
    {
        $config = SiteService::getActiveSiteConfiguration();
        $steps = array_keys($config['fill_steps']);

        $enabledSteps = [];

        foreach ($steps as $step) {
            if (setting('is_account_fill_step_'.$step.'_enabled') === '1') {
                $enabledSteps[] = $step;
            }
        }

        return $enabledSteps;
    }

    /**
     * Получить значение по ключу.
     * @param  \UnitEnum  $key
     * @return string|null
     */
    public static function getByKey(\UnitEnum $key): ?string
    {
        return setting($key->value);
    }

    /**
     * @return \UnitEnum[]
     */
    public function getFrontendSettings(): array
    {
        return FrontendSettingsEnum::cases();
    }
}