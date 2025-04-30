<?php

namespace App\Services\LoanService\Entities;

use App\Models\LoanOffer;
use Illuminate\Support\Collection;

class SourceShowcaseLoansEntity
{
    /**
     * Офферы для витрины.
     * @var Collection<int, LoanOffer>
     */
    public Collection $loanOffers;

    /**
     * Всплывающий оффер для витрины.
     * @var LoanOffer|null
     */
    public ?LoanOffer $featuredLoan;

    /**
     * Параметры для URL.
     * @var array|null
     */
    public ?array $urlParameters;

    /**
     * @param Collection<int, LoanOffer> $loanOffers
     * @param LoanOffer|null $featuredLoan
     * @param array|null $urlParameters
     */
    public function __construct(Collection $loanOffers, ?LoanOffer $featuredLoan, ?array $urlParameters)
    {
        $this->loanOffers = $loanOffers;
        $this->featuredLoan = $featuredLoan;
        $this->urlParameters = $urlParameters;
    }
}