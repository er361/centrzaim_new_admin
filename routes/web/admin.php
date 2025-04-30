<?php

use App\Http\Controllers\Admin\ActionController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ConversionController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\LeadServiceController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\LoanLinkController;
use App\Http\Controllers\Admin\LoanOfferController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\PostbackController;
use App\Http\Controllers\Admin\PostbackTestController;
use App\Http\Controllers\Admin\Report\BannerReportController;
use App\Http\Controllers\Admin\Report\DiffReportController;
use App\Http\Controllers\Admin\Report\RevenueReportController;
use App\Http\Controllers\Admin\Report\SmsReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ShowcaseController;
use App\Http\Controllers\Admin\SmsController;
use App\Http\Controllers\Admin\SmsProviderController;
use App\Http\Controllers\Admin\SourceController;
use App\Http\Controllers\Admin\SourceShowcaseController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\WebmasterController;
use App\Http\Controllers\Admin\WebmasterTemplateController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/home', [HomeController::class, 'index']);

Route::get('login-webmaster-user/{api_id}', function ($api_id) {
    $webmaster = \App\Models\Webmaster::where('api_id', $api_id)->firstOrFail();
    $user = \App\Models\User::whereWebmasterId($webmaster->id)
        ->whereFillStatus(-1)
        ->firstOrFail();

    auth()->login($user);
    return redirect()->route('vitrina');
})->name('login-as');


Route::post('webmaster-templates', WebmasterTemplateController::class)->name('webmaster-templates.store');

Route::get('update-offers', fn() => Artisan::call('offers:update'))->name('update-offers');
// Партнерские программы
Route::resource('sources', SourceController::class)->only(['index', 'edit', 'update']);

// Вебмастера
Route::resource('webmasters', WebmasterController::class)->except(['delete']);

// Витрина займов
Route::resource('showcases', ShowcaseController::class)->only(['index']);
Route::resource('source-showcases', SourceShowcaseController::class)->only(['store']);

// Офферы для витрины займов
Route::resource('loans', LoanController::class)->except(['show']);
Route::resource('loan-links', LoanLinkController::class)->only('store');

// Ссылки для витрины займов
Route::post('/loan-offers/order', [LoanOfferController::class, 'storeOrder'])->name('loan-offers.storeOrder');
Route::resource('loan-offers', LoanOfferController::class)->only(['store', 'update', 'destroy']);
Route::post('/loan-offers/webmaster', [LoanOfferController::class, 'storeWebmasterLoanOffer'])->name('loan-offers.webmaster');
// Клики
Route::resource('actions', ActionController::class)->only(['index']);

// Конверсии
Route::resource('conversions', ConversionController::class)->only(['index']);

// Пользователи
Route::get('users/search', [UsersController::class, 'search'])->name('users.search');
Route::post('users/export', [UsersController::class, 'export'])->name('users.export');
Route::put('users/{user}/unsubscribe', [UsersController::class, 'unsubscribe'])->name('users.unsubscribe');
Route::get('users/{user}/document', [UsersController::class, 'document'])->name('users.document');

Route::resource('users', UsersController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update']);

// Настройки
Route::resource('settings', SettingsController::class)->only(['index', 'store']);

// Платежи
Route::resource('payments', PaymentsController::class)->only(['index', 'show']);

// Постбэки
Route::post('postbacks/export', [PostbackController::class, 'export'])->name('postbacks.export');
Route::get('postbacks/test', [PostbackTestController::class, 'index'])->name('postbacks.test.index');
Route::post('postbacks/test', [PostbackTestController::class, 'store'])->name('postbacks.test.store');
Route::resource('postbacks', PostbackController::class)->only(['index']);

// SMS
Route::resource('sms-providers', SmsProviderController::class)->except(['show']);
Route::resource('sms', SmsController::class);
Route::post('sms_mass_destroy', [SmsController::class, 'massDestroy'])->name('sms.mass_destroy');

// Отчеты
// todo __invoke
Route::get('reports/revenue', [RevenueReportController::class, 'index'])->name('report.revenue');
Route::get('reports/diff', [DiffReportController::class, 'index'])->name('report.diff');
Route::get('reports/banners', [BannerReportController::class, 'index'])->name('report.banner');
Route::get('reports/sms', [SmsReportController::class, 'index'])->name('report.sms');

// Отправка анкет
Route::resource('lead-services', LeadServiceController::class)->only(['index', 'edit', 'update']);

// Баннеры
Route::resource('banners', BannerController::class)->except(['show']);