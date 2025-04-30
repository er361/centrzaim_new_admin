<?php

namespace App\View\Components;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\View\Component;

class SiteStatistics extends Component
{
    public int $activeUsers = 0;
    public int $unsubscribeUsersCount24h = 0;

    private function calc()
    {
        $this->activeUsers = App::make(UserRepository::class)->getUsersForRecurrentCharge(0)->count();
        $this->unsubscribeUsersCount24h = User::where('unsubscribed_at', '>=', now()->subDay())->count();
    }
    public function render(): View
    {
        $this->calc();
        return view('components.site-statistics');
    }
}
