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
     *
     * @param LoanServiceBuilder $loanServiceBuilder
     * @param Request $request
     * @return Factory|Application|View
     */
    public function dashboard(LoanServiceBuilder $loanServiceBuilder, Request $request): Application|Factory|View
    {
        $profileData = UserProfileService::getProfileData(Auth::user());
        $passportData = UserProfileService::getPassportData(Auth::user());

        $webmasterId = request()->cookie('webmaster_id');
        $webmaster = Webmaster::query()->whereApiId($webmasterId)->first();
        $showcase = Showcase::find(Showcase::ID_PRIVATE);
        $source = Source::find(Source::ID_LEADS);
        $user = Auth::user();

        $sourceShowcaseLoansEntity = $loanServiceBuilder
            ->setSource($source)
            ->setWebmaster($webmaster)
            ->setUser($user)
            ->setShowcase($showcase)
            ->setSourceDomain($request->getHost())
            ->getLoanService()
            ->getSourceShowcaseLoans();

        $offers = $this->userOfferService->getOffersNew(
            $showcase,
            $source,
            $webmaster,
            $user
        );

        return view('user.profile', [
            'data' => [
                'offers' => $offers,
                'profile' => $profileData,
                'passport' => $passportData,
            ],
            'sourceShowcaseLoansEntity' => $sourceShowcaseLoansEntity,
        ]);
    }
}
