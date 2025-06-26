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
    $user = \App\Models\User::find(12);

    event(new UserRegistrationFinished($user));
    $postback = \App\Models\Postback::first();
    return  $postback->user->registerExtraData->site_id ?? '-';
});

Route::get('test-sms-direct', function () {
    // Get SMS.ru provider
    $smsProvider = \App\Models\SmsProvider::query()
        ->where('service_id', \App\Models\SmsProvider::SERVICE_SMS_RU)
        ->first();
        
    if (!$smsProvider) {
        return response()->json([
            'error' => 'SMS.ru provider not found'
        ], 404);
    }
    
    // Get a test user with phone
    $user = \App\Models\User::query()
        ->whereNotNull('mphone')
        ->where('mphone', '!=', '')
        ->first();
        
    if (!$user) {
        return response()->json([
            'error' => 'No user with phone found'
        ], 404);
    }
    
    $message = "Test SMS from CentrZaim. Your code: " . rand(1000, 9999);
    
    try {
        $smsService = new \App\Services\SmsService\SmsRu\SmsRuSender();
        $apiId = $smsService->send(
            $smsProvider,
            $user->mphone,
            $message,
            $smsProvider->sender
        );
        
        return response()->json([
            'success' => true,
            'message' => 'SMS sent directly via SMS.ru',
            'api_id' => $apiId,
            'phone' => $user->mphone,
            'text' => $message,
            'provider' => [
                'name' => $smsProvider->name,
                'sender' => $smsProvider->sender,
                'api_login' => $smsProvider->api_login ? 'configured' : 'missing',
                'api_password' => $smsProvider->api_password ? 'configured' : 'missing'
            ]
        ]);
        
    } catch (\Throwable $e) {
        return response()->json([
            'error' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
});

Route::get('test-sms-from-table', function () {
    // Get first active SMS with SMS.ru provider
    $sms = \App\Models\Sms::query()
        ->where('is_enabled', 1)
        ->whereHas('smsProvider', function($q) {
            $q->where('service_id', \App\Models\SmsProvider::SERVICE_SMS_RU);
        })
        ->with(['smsProvider', 'source'])
        ->first();

    if (!$sms) {
        return response()->json([
            'error' => 'No active SMS with SMS.ru provider found',
            'hint' => 'Make sure you have SMS records with is_enabled=1 and SMS.ru provider'
        ], 404);
    }
    
    // Get a test user with phone
    $user = \App\Models\User::query()
        ->whereNotNull('mphone')
        ->where('mphone', '!=', '')
        ->first();
        
    if (!$user) {
        return response()->json([
            'error' => 'No user with phone found'
        ], 404);
    }
    
    // Prepare message
    $message = $sms->text;
    
    // Replace {name} placeholder
    $message = str_replace(\App\Models\Sms::NAME_TEMPLATE, $user->first_name ?? '', $message);
    
    // Replace {link} with a simple test link
    if (str_contains($message, \App\Models\Sms::LINK_TEMPLATE)) {
        $testLink = 'https://example.com/test';
        $message = str_replace(\App\Models\Sms::LINK_TEMPLATE, $testLink, $message);
    }
    
    try {
        $smsService = new \App\Services\SmsService\SmsRu\SmsRuSender();
        $apiId = $smsService->send(
            $sms->smsProvider,
            $user->mphone,
            $message,
            $sms->from ?: $sms->smsProvider->sender
        );
        
        // Save to sms_user table
        \App\Models\SmsUser::create([
            'status' => \App\Models\SmsUser::STATUS_SEND,
            'sms_id' => $sms->id,
            'user_id' => $user->id,
            'api_id' => $apiId,
            'cost' => $sms->smsProvider->sms_cost,
            'service_id' => 0,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'SMS sent from table via SMS.ru',
            'api_id' => $apiId,
            'sms' => [
                'id' => $sms->id,
                'name' => $sms->name,
                'original_text' => $sms->text,
                'sent_text' => $message
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->fname . ' ' . $user->lname,
                'phone' => $user->mphone
            ],
            'provider' => [
                'name' => $sms->smsProvider->name,
                'sender' => $sms->from ?: $sms->smsProvider->sender
            ]
        ]);
        
    } catch (\Throwable $e) {
        return response()->json([
            'error' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
});

Route::get('test-sms', function () {
    // Get first active SMS from database
    $sms = \App\Models\Sms::query()
        ->where('is_enabled', 1)
        ->whereHas('smsProvider')
        ->with(['smsProvider', 'source'])
        ->first();

    if (!$sms) {
        return response()->json([
            'error' => 'No active SMS found in database',
            'requirements' => [
                'is_enabled' => 1,
                'valid sms_provider_id' => 'required'
            ]
        ], 404);
    }

    // Get a test user with phone number
    $user = \App\Models\User::query()
        ->whereNotNull('mphone')
        ->where('mphone', '!=', '')
        ->first();

    if (!$user) {
        return response()->json([
            'error' => 'No user with mobile phone found'
        ], 404);
    }

    // Check provider configuration
    if (!$sms->smsProvider) {
        return response()->json([
            'error' => 'SMS provider not configured',
            'sms_id' => $sms->id
        ], 500);
    }

    $providerInfo = [
        'id' => $sms->smsProvider->id,
        'name' => $sms->smsProvider->name,
        'service_id' => $sms->smsProvider->service_id,
        'api_login' => $sms->smsProvider->api_login ? 'configured' : 'missing',
        'api_password' => $sms->smsProvider->api_password ? 'configured' : 'missing',
        'sender' => $sms->smsProvider->sender,
        'sms_cost' => $sms->smsProvider->sms_cost
    ];

    \Illuminate\Support\Facades\Log::info('Test SMS: Starting', [
        'sms_id' => $sms->id,
        'user_id' => $user->id,
        'phone' => $user->mphone,
        'showcase_id' => $sms->showcase_id,
        'provider' => $providerInfo
    ]);

    try {
        // Generate link manually to test
        if ($sms->showcase_id === null) {
            $link = route('front.sms.redirect', [
                'sms' => $sms->id,
                'user_id' => $user->id,
                'key' => $sms->getSecretKey((string)$user->id),
            ]);
            
            \Illuminate\Support\Facades\Log::info('Test SMS: Generated link', ['link' => $link]);
        }

        $smsSender = new \App\Services\SmsService\SmsSender();
        
        if ($sms->showcase_id === null) {
            \Illuminate\Support\Facades\Log::info('Test SMS: Sending external link SMS');
            $smsSender->sendExternalLinkSms($sms, $user);
        } else {
            \Illuminate\Support\Facades\Log::info('Test SMS: Sending external showcase SMS');
            $smsSender->sendExternalShowcaseSms($sms, $user);
        }
        
        // Check the status in pivot table
        $pivotRecord = $user->sms()
            ->where('sms_id', $sms->id)
            ->orderBy('pivot_created_at', 'desc')
            ->first();
        
        $response = [
            'success' => true,
            'message' => 'SMS sent successfully',
            'sms' => [
                'id' => $sms->id,
                'name' => $sms->name,
                'type' => $sms->type,
                'text' => $sms->text,
                'provider' => $providerInfo,
                'showcase_id' => $sms->showcase_id,
                'link' => $sms->link
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->fname . ' ' . $user->lname,
                'phone' => $user->mphone
            ]
        ];
        
        if ($pivotRecord) {
            $response['send_details'] = [
                'status' => $pivotRecord->pivot->status,
                'api_id' => $pivotRecord->pivot->api_id,
                'cost' => $pivotRecord->pivot->cost,
                'error' => $pivotRecord->pivot->error
            ];
        }
        
        return response()->json($response);
        
    } catch (\App\Services\SmsService\Exceptions\SmsSenderException $e) {
        // Get the previous exception which has the actual error
        $previousException = $e->getPrevious();
        $actualMessage = $previousException ? $previousException->getMessage() : $e->getMessage();
        
        $errorDetails = [
            'exception_class' => get_class($e),
            'message' => $actualMessage ?: 'Empty message',
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
        
        if ($previousException) {
            $errorDetails['previous_exception'] = [
                'class' => get_class($previousException),
                'message' => $previousException->getMessage(),
                'file' => $previousException->getFile(),
                'line' => $previousException->getLine()
            ];
        }
        
        \Illuminate\Support\Facades\Log::error('Test SMS: SmsSenderException', $errorDetails);
        
        // Check if there's a failed record in sms_user table
        $failedRecord = \App\Models\SmsUser::query()
            ->where('user_id', $user->id)
            ->where('sms_id', $sms->id)
            ->where('status', \App\Models\SmsUser::STATUS_SENDING_FAILED)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($failedRecord) {
            $errorDetails['sms_user_error'] = $failedRecord->error;
        }
        
        return response()->json([
            'error' => 'SmsSenderException',
            'message' => $actualMessage ?: 'Empty error message',
            'details' => $errorDetails,
            'trace' => config('app.debug') ? explode("\n", $e->getTraceAsString()) : null
        ], 500);
    } catch (\Throwable $e) {
        $errorDetails = [
            'exception_class' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
        
        \Illuminate\Support\Facades\Log::error('Test SMS: General exception', $errorDetails);
        
        return response()->json([
            'error' => 'Exception',
            'message' => $e->getMessage(),
            'details' => $errorDetails,
            'trace' => config('app.debug') ? explode("\n", $e->getTraceAsString()) : null
        ], 500);
    }
});