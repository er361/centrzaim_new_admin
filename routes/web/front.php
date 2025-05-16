<?php

use App\Facades\UserOfferService;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SmsLoginController;
use App\Http\Controllers\Front\AccountController;
use App\Http\Controllers\Front\ActivationController;
use App\Http\Controllers\Front\FillController;
use App\Http\Controllers\Front\IndexController;
use App\Http\Controllers\Front\LoanOfferController;
use App\Http\Controllers\Front\LoansPageController;
use App\Http\Controllers\Front\Miazaim\CancelSubscriptionController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\Front\SmsController;
use App\Http\Controllers\Front\SourcePageController;
use App\Http\Controllers\ResendSmsController;
use App\Http\Controllers\VitrinaController;
use App\Http\Middleware\DoNotNeedToActivate;
use App\Http\Middleware\NeedToActivate;
use App\Http\Middleware\SaveWebmasterToCookieMiddleware;
use App\Http\Middleware\VitrinaMiddleware;
use Illuminate\Support\Facades\Route;


Route::get('/vitrina', [VitrinaController::class, 'index'])->name('vitrina')
    ->middleware([VitrinaMiddleware::class, 'dashboard']);

Route::get('/public/vitrina', [VitrinaController::class, 'public'])->name('public.vitrina');
Route::get('/preview', [VitrinaController::class, 'preview'])->name('preview');

Route::get('resend/{phone}', ResendSmsController::class)->name('sms.resend');

Route::group(['as' => 'front.'], function () {
    Route::get('/', IndexController::class)->name('index');

    Route::get('/loans', [LoansPageController::class, 'index'])->name('loans');
    Route::get('/public-loans', [LoansPageController::class, 'public'])->name('public.loans');
    Route::get('/private-loans', [LoansPageController::class, 'private'])->name('private.loans');
    Route::get('/loan-offer/{loanOffer}', LoanOfferController::class)->name('loan-offers.show');

    // Посадочные страницы
    Route::get('lp/{source}', SourcePageController::class)->name('lp.index');

    // SMS
    Route::get('sms/{sms}', [SmsController::class, 'redirect'])->name('sms.redirect');

    // Отписка
    Route::prefix('unsubscribe')
        ->name('unsubscribe.')
        ->group(function () {
            Route::get('/', [CancelSubscriptionController::class, 'index'])->name('index');
            Route::get('/confirm-page/{phone}', [CancelSubscriptionController::class, 'confirmPage'])->name('confirm.page');
            Route::get('/not-found/{phone}', [CancelSubscriptionController::class, 'notFound'])->name('not.found');
            Route::get('/success', [CancelSubscriptionController::class, 'success'])->name('success');
            Route::post('/send-code', [CancelSubscriptionController::class, 'sendCode'])
                ->name('send.code')->middleware('throttle:5');
            Route::post('/confirm', [CancelSubscriptionController::class, 'confirm'])->name('confirm.code');
        });

});

Route::group(['prefix' => 'my', 'as' => 'account.', 'middleware' => 'auth'], function () {
    Route::group(['prefix' => 'activation', 'as' => 'activation.', 'middleware' => [DoNotNeedToActivate::class]], function () {
        Route::get('/', [ActivationController::class, 'index'])->name('index');
        Route::post('/', [ActivationController::class, 'store'])->middleware(['throttle:5'])->name('store');
        Route::post('/validateCode', [ActivationController::class, 'validateCode'])->name('validateCode');
        Route::get('/resend', [ActivationController::class, 'resend'])->middleware(['throttle:5'])->name('resend');
        
        // Методы активации
        Route::prefix('method')->as('method.')->group(function () {
            Route::get('/telegram', [ActivationController::class, 'telegramActivation'])->name('telegram');
            Route::get('/telegram/code', [ActivationController::class, 'telegramActivationPage'])->name('telegram.code');
            Route::get('/sms', [ActivationController::class, 'smsActivation'])->name('sms');
        });
    });

    Route::group(['middleware' => [NeedToActivate::class]], function () {
        Route::group(['prefix' => 'fill'], function () {
            Route::get('/', [FillController::class, 'index'])->name('fill.index');
            Route::post('/', [FillController::class, 'store'])->name('fill.store');
            Route::post('/validate', [FillController::class, 'validateStep'])->name('fill.validate');
        });

        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/form', [PaymentController::class, 'form'])->name('payments.form');
        Route::get('payments/result', [PaymentController::class, 'result'])->name('payments.result')
            ->withoutMiddleware(['auth', NeedToActivate::class]); // Инкогнито
    });

    Route::group(['middleware' => ['dashboard']], function () {
        Route::get('/dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
    });
});

// Authentication Routes...
Route::group(['middleware' => 'guest'], function () {
    Route::get('login', [SmsLoginController::class, 'index'])->name('sms.login');
    Route::get('confirm/{phone}', [SmsLoginController::class, 'confirmPage'])->name('sms.confirm-page');

    Route::post('sendCode', [SmsLoginController::class, 'sendCode'])->name('sms.send-code');
    Route::post('confirm', [SmsLoginController::class, 'confirm'])->name('sms.confirm');
});


Route::prefix('admin')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('auth.login');
    Route::post('login', [LoginController::class, 'login'])->name('auth.login.store');
});

// Allow both POST and GET requests for logout
Route::post('logout', [LoginController::class, 'logout'])->name('auth.logout');
Route::get('logout', [LoginController::class, 'logout'])->name('auth.logout.get');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])
    ->name('auth.register');

Route::post('register', [RegisterController::class, 'register'])
    ->name('auth.register.store');
Route::post('register/validate', [RegisterController::class, 'validateRegister'])
    ->name('auth.register.validate');

Route::get('banner-test',fn()=> view('banner_test'))->name('banner-test');

Route::get('/sp-test', function () {
    return view('speed-test');
});

Route::get('/speed-test', function () {
    // Засекаем время начала выполнения на сервере
    $startTime = microtime(true);

    // Здесь можно добавить аналогичную логику, которая выполняется в Livewire компоненте
    // Например, запросы к базе данных

    // Для имитации нагрузки можно использовать sleep
    // sleep(1); // раскомментируйте для имитации нагрузки

    // Простой запрос к БД (замените на похожий запрос из вашего компонента)
    // $users = DB::table('users')->limit(10)->get();

    // Вычисляем время выполнения на сервере в миллисекундах
    $serverTime = (microtime(true) - $startTime) * 1000;

    return response()->json([
        'status' => 'success',
        'message' => 'Speed test completed',
        'server_time' => $serverTime,
        'timestamp' => microtime(true)
    ]);
});

