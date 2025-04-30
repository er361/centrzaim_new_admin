<?php

namespace App\Providers;

use App\Actions\CreateWebmasterShowcaseAction;
use App\Events\UserActivated;
use App\Events\UserOnLandingPageEvent;
use App\Events\UserPaymentSuccessful;
use App\Events\UserRegistrationFinished;
use App\Events\UserRegistrationStep1DoneEvent;
use App\Events\WebmasterRegistered;
use App\Listeners\CheckUserFccpListener;
use App\Listeners\CreateWebmasterShowcase;
use App\Listeners\RegisterCookiesListener;
use App\Listeners\SendUserPostback;
use App\Listeners\SendUserPostbackAfterFill;
use App\Listeners\SendUserPostbackAfterPayment;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var  array<string, array<int, string>>
     */
    protected $listen = [
        UserRegistrationFinished::class => [
            SendUserPostback::class,
        ],
        UserPaymentSuccessful::class => [
            SendUserPostback::class,
        ],
        UserActivated::class => [
            SendUserPostback::class,
        ],
        UserRegistrationStep1DoneEvent::class => [
            CheckUserFccpListener::class,
        ],
        WebmasterRegistered::class => [
            CreateWebmasterShowcase::class
        ],
        UserOnLandingPageEvent::class => [
            RegisterCookiesListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        //
    }
}
