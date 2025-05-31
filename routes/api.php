<?php

use App\Events\UserRegistrationFinished;
use App\Http\Controllers\Api\V1\ActivationController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PostbackController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Middleware\ApiTokenMiddleware;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/v1', 'as' => 'api.'], function () {
    // Postback
    Route::get('postback/store', [PostbackController::class, 'store'])->name('postback.store');

    // Платежи
    Route::post('payments/callback/{method}', [PaymentController::class, 'process'])->name('payment.callback');
    Route::apiResource('payments', PaymentController::class)->only(['show']);

    // Loans
    Route::apiResource('loans', LoanController::class)->only(['index']);

    Route::apiResource('users', UserController::class)
        ->only(['index'])
        ->middleware(ApiTokenMiddleware::class);
});


Route::middleware('auth:sanctum')
    ->prefix('v1')
    ->group(function () {

        Route::prefix('activation')->group(function () {
            Route::get('code', [ActivationController::class, 'generateCode']);
            Route::post('confirm', [ActivationController::class, 'confirmCode']);
        });

        Route::get('user-info/{user}', [UserController::class, 'info'])->name('user.info');
    });


Route::get('/api/documentation', '\L5Swagger\Http\Controllers\SwaggerController@api')->name('l5swagger.api');

Route::get('test', function () {
    $user = \App\Models\User::find(7650);

    event(new UserRegistrationFinished($user));
});