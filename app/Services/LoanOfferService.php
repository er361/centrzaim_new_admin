<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanOffer;
use App\Models\Webmaster;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class LoanOfferService
{
    public function handleLoanOffers(int $loanId, Collection $loanOffers): void
    {
        $loan = Loan::query()
            ->with([
                'loanOffers' => function (HasMany $query) {
                    $query->withTrashed(); // @phpstan-ignore-line
                },
            ])->find($loanId);

        $loanOffers->each(function (array $sourceLoanOffers, int $sourceId) use ($loan) {
            $sourceLoanOffers = collect($sourceLoanOffers);

            $sourceLoanOffers->each(function (?int $loanLinkId, int $showcaseId) use ($loan, $sourceId) {
                $this->processLoanOffer($loan, $sourceId, $showcaseId, $loanLinkId, null);
            });
        });
    }

    public function handleWebmasterLoanOffers(int $loanId, Collection $loanOffers): void
    {
        $loan = Loan::query()
            ->with([
                'loanOffers' => function (HasMany $query) {
                    $query->withTrashed(); // Учитываем soft-deleted записи
                },
            ])
            ->find($loanId);

        // Обрабатываем каждый оффер из запроса
        $loanOffers->each(function (array $offerData) use ($loan) {
            $webmasterId = $offerData['webmaster_id'];
            $showcases = $offerData['showcases'];
            $sourceId = Webmaster::find($webmasterId)->source_id;

            collect($showcases)->each(function (?int $loanLinkId, int $showcaseId) use ($loan, $sourceId, $webmasterId) {
                $this->processLoanOffer($loan, $sourceId, $showcaseId, $loanLinkId, $webmasterId);
            });
        });
    }

    private function processLoanOffer(Loan $loan, int $sourceId, int $showcaseId, ?int $loanLinkId, ?int $webmasterId): void
    {
        $currentLoanOffers = $loan->loanOffers
            ->where('source_id', $sourceId)
            ->where('showcase_id', $showcaseId)
            ->where('webmaster_id', $webmasterId);


        $existingLoanOfferFullMatch = $currentLoanOffers
            ->where('loan_link_id', $loanLinkId)
            ->first();

        if ($existingLoanOfferFullMatch === null) {
            $this->deleteLoanOffers($currentLoanOffers);

            if ($loanLinkId !== null) {
                $this->createLoanOffer($loan, $sourceId, $showcaseId, $loanLinkId, $webmasterId);
            }

            return;
        }

        $this->deleteOtherLoanOffers($currentLoanOffers, $existingLoanOfferFullMatch);
        $this->restoreLoanOffer($existingLoanOfferFullMatch);
    }

    private function deleteLoanOffers(Collection $loanOffers): void
    {
        $loanOffers->each(function (LoanOffer $loanOffer) {
            if (!$loanOffer->trashed()) {
                $loanOffer->delete();
            }
        });
    }

    private function deleteOtherLoanOffers(Collection $loanOffers, LoanOffer $keepLoanOffer): void
    {
        $loanOffers->each(function (LoanOffer $loanOffer) use ($keepLoanOffer) {
            if (!$loanOffer->trashed() && !$loanOffer->is($keepLoanOffer)) {
                $loanOffer->delete();
            }
        });
    }

    private function restoreLoanOffer(LoanOffer $loanOffer): void
    {
        if ($loanOffer->trashed()) {
            $loanOffer->restore();
        }
    }

    private function createLoanOffer(Loan $loan, int $sourceId, int $showcaseId, int $loanLinkId, ?int $webmasterId = null): void
    {
        LoanOffer::query()->create([
            'loan_id' => $loan->id,
            'showcase_id' => $showcaseId,
            'source_id' => $sourceId,
            'loan_link_id' => $loanLinkId,
            'webmaster_id' => $webmasterId,
        ]);
    }
}
