<?php

namespace App\View\Components;

use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PaymentInfo extends Component
{
    public function formatDate($date)
    {
        return date('d.m.Y, H:i', strtotime($date));
    }

    public function mapStatus($status)
    {
        return Payment::STATUSES[$status];
    }

    public function render(): View
    {
        $user = \Auth::user()->load('payments');
        $user->payments->each(function (Payment $payment) {
            $payment->amount = number_format($payment->amount, 2, '.', ' ');
        });
        return view('components.payment-info', compact('user'));
    }
}
