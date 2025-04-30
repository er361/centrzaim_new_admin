<?php

namespace App\Services\LoanService;

use App\Models\Showcase;
use App\Models\Sms;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use App\Repositories\LoanOfferRepository;
use App\Services\LoanService\Entities\SourceShowcaseLoansEntity;
use InvalidArgumentException;

class LoanService
{
    /**
     * @param Webmaster|null $webmaster
     * @param Showcase|null $showcase
     * @param User|null $user
     * @param string|null $sourceDomain
     * @param Sms|null $sms
     * @param Source|null $source
     * @param LoanOfferRepository $loanOfferRepository
     */
    public function __construct(
        protected ?Webmaster           $webmaster,
        protected ?Showcase            $showcase,
        protected null|User                $user,
        protected ?string              $sourceDomain,
        protected ?Sms                 $sms,
        protected ?Source              $source,
        protected LoanOfferRepository  $loanOfferRepository
    )
    {
        if ($this->sms === null && $this->showcase === null) {
            throw new InvalidArgumentException('Для создания LoanService нужно передать или SMS, или Showcase.');
        }
    }

    /**
     * Получить офферы для размещения на витрине.
     * @return SourceShowcaseLoansEntity
     */
    public function getSourceShowcaseLoans(): SourceShowcaseLoansEntity
    {
        $loanOffers = $this->loanOfferRepository
            ->getPageLoans($this->webmaster, $this->showcase, $this->source)
            ->get();

        $featuredLoanOffers = $this->loanOfferRepository
            ->getFeaturedLoan($this->webmaster, $this->showcase, $this->source)
            ->get();

        return new SourceShowcaseLoansEntity(
            $loanOffers->toBase(),
            $featuredLoanOffers->first(),
            $this->getUrlParameters()
        );
    }

    /**
     * Получить список параметров текущей ссылки.
     * @return array<string, string>
     */
    protected function getUrlParameters(): array
    {
        $params = [];

        if ($this->sms !== null) {
            $params['sms_id'] = (string)$this->sms->id;
        }

        if ($this->user !== null) {
            $params['user_id'] = (string)$this->user->id;
        }

        if ($this->sourceDomain !== null) {
            $params['source_domain'] = (string)$this->sourceDomain;
        }

        if ($this->webmaster !== null) {
            $params['webmaster_id'] = (string)$this->webmaster->id;
        }

        return $params;
    }
}