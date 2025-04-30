<?php

namespace App\Providers;

use App\Actions\GenerateUtmFromTemplate;
use App\Services\ActivationService\ActivationServiceInterface;
use App\Services\ActivationService\SmsActivationService;
use App\Services\BannerService\AdsFinSyncStatisticsService;
use App\Services\LinkShortenService\GooSuLinkShortener;
use App\Services\LinkShortenService\LeadsLinkShortener;
use App\Services\LinkShortenService\LinkShortenServiceContract;
use App\Services\PaymentService\Contracts\PaymentServiceInterface;
use App\Services\PaymentService\Impaya\ImpayaPaymentService;
use App\Services\RedirectUrlService;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use App\Services\UserProfileService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Contracts\EntriesRepository;
use Laravel\Telescope\Storage\DatabaseEntriesRepository;
use Mockery;
use Mockery\Mock;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // Добавляем путь к представлениям вручную
//        View::addLocation(resource_path('views'));

        Schema::defaultStringLength(191);

        if ($this->app->environment() === 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }

        $this->app->bind(
            ActivationServiceInterface::class,
            SmsActivationService::class
        );

        $this->app->bind(ActivationServiceInterface::class, function () {
            if(config('sms_activation.mode') === 'local') {
                return Mockery::mock(ActivationServiceInterface::class, function ($mock) {
                    // Подменяем метод отправки SMS
                    $mock->shouldReceive('sendCode')
                        ->andReturn(null); // Метод всегда возвращает true
                    $mock->shouldReceive('resendCode')
                        ->andReturn(null); // Метод всегда возвращает true
                    $mock->shouldReceive('validateCode')
                        ->andReturn(true); // Метод всегда возвращает true
                });
            }
            return new SmsActivationService();
        });

        $this->app->bind(
            PaymentServiceInterface::class,
            ImpayaPaymentService::class
        );

        $this->app->singleton(
            SettingsService::class,
            SettingsService::class
        );

        $this->app->singleton(LinkShortenServiceContract::class, GooSuLinkShortener::class);

        $this->app->singleton(UserProfileService::class, function ($app) {
            return new UserProfileService();
        });

        $this->app->singleton(RedirectUrlService::class, function ($app) {
            return new RedirectUrlService($app->make(GenerateUtmFromTemplate::class));
        });

        Model::preventLazyLoading(app()->isLocal());



    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->usePublicPath(__DIR__.'/../../public_html');

        $this->app->bind(AdsFinSyncStatisticsService::class, function () {
            $token = config('services.banner.adsfin.token');
            return new AdsFinSyncStatisticsService(
                token:  $token,
            );
        });
    }
}
