<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class BanksNotificationModal extends Component
{
    public $shouldShow = true;
    public function render(): View
    {
        $settings = setting()->all();
        $key = 'show_banks_notification_modal';

        $shouldShow = Arr::get($settings, $key, '0');
        if ($shouldShow === '1') {
            $this->shouldShow = true;
        } else {
            $this->shouldShow = false;
        }
        return view('components.banks-notification-modal');
    }
}
