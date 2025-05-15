<?php

namespace App\Http\Controllers\Front;

use App\Facades\UserProfileService;
use App\Http\Controllers\Controller;
use App\Models\Showcase;
use App\Models\Source;
use App\Models\Webmaster;
use App\Services\LoanService\LoanServiceBuilder;
use App\Services\UserOfferService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class AccountController extends Controller
{

    private UserOfferService $userOfferService;

    public function __construct(UserOfferService $userOfferService)
    {
        $this->userOfferService = $userOfferService;
    }
    /**
     * Главная страница аккаунта.
     * Редирект на страницу витрины.
     *
     * @param LoanServiceBuilder $loanServiceBuilder
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dashboard(LoanServiceBuilder $loanServiceBuilder, Request $request)
    {
        return redirect()->route('vitrina');
    }
}
