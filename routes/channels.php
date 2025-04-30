<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Models\Payment;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('payment.{paymentId}', function ($user, $paymentId) {
    /** @var Payment $payment */
    $payment = Payment::query()->findOrNew($paymentId);
    return $user->id === $payment->user_id;
});
