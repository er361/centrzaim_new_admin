<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoanOfferStoreOrderRequest;
use App\Http\Requests\Admin\LoanOfferStoreRequest;
use App\Http\Requests\LoanWebmasterOfferStoreRequest;
use App\Models\LoanOffer;
use App\Services\LoanOfferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class LoanOfferController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param LoanOfferStoreRequest $request
     * @param LoanOfferService $service
     * @return RedirectResponse
     */
    public function store(LoanOfferStoreRequest $request, LoanOfferService $service): RedirectResponse
    {
        $loanOffers = $request->collect('loan_offers');

        $service->handleLoanOffers($request->input('loan_id'), $loanOffers);

        return Redirect::back()
            ->with('success', 'Настройки размещения оффера на витрине успешно сохранены.');
    }

    public function storeWebmasterLoanOffer(LoanWebmasterOfferStoreRequest $request, LoanOfferService $service): RedirectResponse
    {
        $loanOffers = $request->collect('loan_offers');
        $service->handleWebmasterLoanOffers($request->input('loan_id'), $loanOffers);

        return Redirect::back()
            ->with('success', 'Настройки размещения оффера на витрине успешно сохранены.');
    }

    public function update(LoanOffer $loanOffer): void
    {
        $loanOffer->update([
            'is_backup' => request('is_backup'),
        ]);
    }

    public function destroy(LoanOffer $loanOffer): void
    {
        $loanOffer->delete();
    }

    /**
     * Сохранить порядок предложений на витрине.
     * @param LoanOfferStoreOrderRequest $request
     * @return Response
     */
    public function storeOrder(LoanOfferStoreOrderRequest $request): Response
    {
        $loanOffers = LoanOffer::query()
            ->whereIn('id', $request->input('loan_offers'))
            ->get()
            ->keyBy('id');

        $request->collect('loan_offers')
            ->each(function (int $id, int $order) use ($loanOffers) {
                $loanOffers->get($id)->update([
                    'priority' => $order + 1,
                ]);
            });

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
