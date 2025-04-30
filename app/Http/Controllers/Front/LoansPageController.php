<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Showcase;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use App\Services\LoanService\LoanServiceBuilder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LoansPageController extends Controller
{
    /**
     * Витрина займов.
     *
     * @param Request $request
     * @param LoanServiceBuilder $loanServiceBuilder
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View
     *
     * @todo Возможно, предпросмотр стоит вынести на отдельный адрес?
     */
    public function index(Request $request, LoanServiceBuilder $loanServiceBuilder): Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if($request->has('webmaster_id')) {
            $webmasterId = $request->input('webmaster_id');
        } else {
            $webmasterId = request()->cookie('webmaster_id');
        }

        $webmaster = Webmaster::query()->whereApiId($webmasterId)->first();
        $source = null;

        // Функционал предпросмотра витрины в панели администратора
        if ($request->has('source_id')) {
            /** @var null|Source $source */
            $source = Source::query()->find($request->input('source_id'));
        }

        if ($request->has('showcase_id')) {
            $showcase = Showcase::query()->find($request->input('showcase_id'));
        }

        $showcase ??= Showcase::query()
            ->whereIsPublic()
            ->whereExternalUrlIsNull()
            ->first();

        /** @var User $user */
        $user = Auth::user();

        $sourceShowcaseLoansEntity = $loanServiceBuilder
            ->setSource($source)
            ->setWebmaster($webmaster)
            ->setUser($user)
            ->setShowcase($showcase)
            ->setSourceDomain($request->getHost())
            ->getLoanService()
            ->getSourceShowcaseLoans();

        return view('pages.loans-admin-integration', compact('sourceShowcaseLoansEntity'));
    }

    public function public()
    {
        return redirect()->route('front.loans',['showcase_id' => 2, 'source_id' => 1]);
    }

    public function private()
    {
        return redirect()->route('front.loans',['showcase_id' => 1, 'source_id' => 1]);
    }

}