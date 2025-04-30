<?php

namespace App\Http\Controllers\Front;

use App\Http\Actions\ProcessLoanOfferClickAction;
use App\Models\LoanOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoanOfferController
{
    /**
     * Клик по офферу.
     *
     * @param Request $request
     * @param LoanOffer $loanOffer
     * @param ProcessLoanOfferClickAction $action
     * @return RedirectResponse
     */
    public function __invoke(Request $request, LoanOffer $loanOffer, ProcessLoanOfferClickAction $action): RedirectResponse
    {
        $url = $action->handle($request, $loanOffer);
        return redirect()->away(
            $url
        );
    }
}