<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use App\Repositories\LoanOfferRepository;
use App\Services\LoanService\LoanServiceBuilder;
use App\Services\OffersChecker\Settings;
use App\Services\UserOfferService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelIdea\Helper\App\Models\_IH_Source_C;

class VitrinaController extends Controller
{

    private UserOfferService $userOfferService;

    public function __construct(UserOfferService $userOfferService)
    {
        $this->userOfferService = $userOfferService;
    }

    public function preview(LoanServiceBuilder $loanServiceBuilder, Request $request)
    {
        if (
            !$request->has('webmaster_id') ||
            !$request->has('source_id') ||
            !$request->has('showcase_id')
        )
            return redirect()->back()->withErrors('Не переданы обязательные параметры');

        $webmaster = Webmaster::query()->find($request->input('webmaster_id'));
        $source = Source::find($request->input('source_id'));
        $showcase = Showcase::find($request->input('showcase_id'));

        $sourceShowcaseLoansEntity = $loanServiceBuilder
            ->setSource($source)
            ->setWebmaster($webmaster)
            ->setShowcase($showcase)
            ->setSourceDomain($request->getHost())
            ->getLoanService()
            ->getSourceShowcaseLoans();

        $offers = $this->userOfferService->getOffersNew(
            $showcase,
            $source,
            $webmaster,
            null
        );

        if ($showcase->id === Showcase::ID_PUBLIC)
            return view('pages.public-vitrina', [
                'sourceShowcaseLoansEntity' => $sourceShowcaseLoansEntity,
                'offers' => $offers
            ]);
        else
            return view('pages.loans', [
                'sourceShowcaseLoansEntity' => $sourceShowcaseLoansEntity,
                'offers' => $offers
            ]);
    }

    public function index(LoanServiceBuilder $loanServiceBuilder, Request $request)
    {
        $user = Auth::user();
        $webmaster = $user->webmaster;
        $source = $webmaster?->source;
        $showcase = Showcase::find(Showcase::ID_PRIVATE);
//        dump('All cookies:', request()->cookie());
//        dump('$_COOKIE global:', $_COOKIE);
//        dd('Cookie data is empty');

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

        return view('pages.loans', [
            'sourceShowcaseLoansEntity' => $sourceShowcaseLoansEntity,
            'offers' => $offers
        ]);

    }

    public function public(Request $request, LoanServiceBuilder $loanServiceBuilder)
    {
        $user = Auth::user();

        if ($user) {
            $webmaster = $user->webmaster;
        } else {
            $webmaster = Webmaster::find($request->cookie('webmaster_id'));
        }

        $source = $webmaster?->source;
        $showcase = Showcase::find(Showcase::ID_PUBLIC);

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

        return view('pages.public-vitrina', [
            'sourceShowcaseLoansEntity' => $sourceShowcaseLoansEntity,
            'offers' => $offers
        ]);
    }
}
