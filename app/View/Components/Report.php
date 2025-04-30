<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Report extends Component
{
    public function render(): View
    {
        $sendAt = \Auth::user()->created_at->format('d.m.Y H:i');
        return view('components.report', compact('sendAt'));
    }
}
