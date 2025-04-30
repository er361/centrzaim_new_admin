<?php

namespace App\Http\Middleware;

use App\Models\Payment;
use App\Models\User;
use App\Services\SettingsService\SettingsService;
use Closure;
use Illuminate\Support\Facades\Auth;

class PaymentRequired
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();

        $isPaymentsEnabled = SettingsService::isPaymentsEnabled();
        $isPaymentsExists = $user->payments()->where('type', Payment::TYPE_DEFAULT)->exists();
        $isDisabled = $user->is_disabled;
        $isPaymentRequired = $user->is_payment_required;

        if ($isPaymentsEnabled && !$isPaymentsExists && !$isDisabled && $isPaymentRequired) {
            return redirect()->route('account.payments.index');
        }

        return $next($request);
    }
}