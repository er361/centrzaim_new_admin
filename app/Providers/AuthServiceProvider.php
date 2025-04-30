<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\User;
use App\Models\Webmaster;
use App\Policies\PaymentPolicy;
use App\Policies\UserPolicy;
use App\Policies\WebmasterPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Payment::class => PaymentPolicy::class,
        Webmaster::class => WebmasterPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $permissions = [
            // Пользователи
            'user_full_access' => [1, 100],
            'user_list' => [1, 4, 100],
            'user_view' => [1, 3, 4, 100],
            'user_unsubscribe' => [1, 3, 100],
            'user_search' => [1, 3, 100],
            'user_create' => [1, 100],
            'user_edit' => [1, 100],

            // Вебмастера
            'webmaster_access' => [1, 100],
            'webmaster_create' => [1, 100],
            'webmaster_edit' => [1, 100],
            'webmaster_view' => [1, 100],

            // Витрина займов
            'loan_access' => [1, 100],
            'loan_show' => [1, 100],
            'loan_create' => [1, 100],
            'loan_edit' => [1, 100],
            'loan_delete' => [1, 100],

            // Ссылки для витрины займов
            'loan_link_create' => [1, 100],

            // Предложения на витрине займов
            'loan_offer_create' => [1, 100],
            'loan_offer_store_order' => [1, 100],

            // Витрины займов
            'showcase_access' => [1, 100],

            // Витрины займов для источника
            'source_showcase_create' => [1, 100],

            // SMS
            'sms_access' => [1, 100],
            'sms_create' => [1, 100],
            'sms_edit' => [1, 100],
            'sms_delete' => [1, 100],

            // Баннеры
            'banner_access' => [1, 100],
            'banner_create' => [1, 100],
            'banner_edit' => [1, 100],
            'banner_delete' => [1, 100],

            // Постбэки
            'postback_access' => [1, 100],

            // Аккаунты SMS
            'sms_provider_access' => [1, 100],
            'sms_provider_create' => [1, 100],
            'sms_provider_edit' => [1, 100],
            'sms_provider_delete' => [1, 100],

            // Отчеты
            'report_access' => [1, 100], // Отчет на главной странице
            'revenue_report_access' => [1, 4, 100],
            'diff_report_access' => [1, 100],
            'banner_report_access' => [1, 100],
            'sms_report_access' => [1, 100],

            // Партнерские программы
            'source_access' => [1, 100],
            'source_edit' => [1, 100],

            // Платежи
            'payment_access' => [1, 100],
            'payment_view' => [1, 100],

            // Отправка анкет
            'lead_service_access' => [1, 100],
            'lead_service_edit' => [1, 100],

            // Настройки
            'setting_access' => [1, 100],
            'setting_edit' => [1, 100],

            // Конверсии
            'action_access' => [1, 100],

            // Клики
            'conversion_access' => [1, 100],
        ];

        foreach ($permissions as $permission => $roles) {
            Gate::define($permission, function ($user) use ($roles) {
                return in_array($user->role_id, $roles, true);
            });
        }
    }
}
