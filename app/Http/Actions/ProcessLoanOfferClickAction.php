<?php

namespace App\Http\Actions;

use App\Models\LoanOffer;
use App\Models\Sms;
use App\Models\User;
use App\Models\Webmaster;
use App\Services\LinkService\LinkServiceFactory;
use Illuminate\Http\Request;

class ProcessLoanOfferClickAction
{
    /**
     * @var LinkServiceFactory
     */
    protected LinkServiceFactory $linkServiceFactory;

    /**
     * @param LinkServiceFactory $linkServiceFactory
     */
    public function __construct(LinkServiceFactory $linkServiceFactory)
    {
        $this->linkServiceFactory = $linkServiceFactory;
    }

    /**
     * @param Request $request
     * @param LoanOffer $loanOffer
     * @return string Ссылка для переадресации пользователя
     */
    public function handle(Request $request, LoanOffer $loanOffer): string
    {
        $linkService = $this->linkServiceFactory->getCreatorInstance(
            $loanOffer->loanLink->source
        );

        $showcase = $loanOffer->showcase;
        $user = null;
        $webmaster = null;
        $sourceDomain = $request->input('source_domain');

        if ($request->has('user_id')) {
            $user = User::query()->find($request->input('user_id'));
        }

        if ($request->has('webmaster_id')) {
            $webmaster = Webmaster::query()->find($request->input('webmaster_id'));
        } else if ($request->cookie('webmaster_id')) {
            $webmaster = Webmaster::query()->find($request->cookie('webmaster_id'));
        } else {
            \Log::warning('Webmaster not found', ['request' => $request->all()]);
        }

        if ($request->has('sms_id')) {
            $sms = Sms::query()->find($request->input('sms_id'));

            if ($sms === null) {
                return route('front.loans');
            }

            return $linkService->getSmsLink(
                $loanOffer->loanLink->link,
                $user,
                $sms,
                $sourceDomain
            );
        }

        if ($showcase->is_public || $showcase->external_url !== null || $user === null) {
            return $linkService->getPublicDashboardLink(
                $loanOffer->loanLink->link,
                $webmaster,
                $sourceDomain
            );
        }

        return $linkService->getUserDashboardLink(
            $loanOffer->loanLink->link,
            $user,
            $sourceDomain
        );
    }
}