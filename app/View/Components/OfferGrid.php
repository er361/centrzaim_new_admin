<?php

namespace App\View\Components;

use App\Facades\UserOfferService;
use App\Models\Loan;
use App\Models\LoanOffer;
use App\Services\LoanService\Entities\SourceShowcaseLoansEntity;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class OfferGrid extends Component
{
    const string OFFER_TYPE_OLD = 'old';
    const string OFFER_TYPE_NEW = 'new';

    //if type old - $offers is array  else $offers is Collection
    public array|Collection $offers;

    public string $offersType;

    private ?SourceShowcaseLoansEntity $sourceShowcaseLoansEntity;

    public function __construct($offersType = self::OFFER_TYPE_OLD, $offers = [], $sourceShowcaseLoansEntity = null)
    {
        $this->offers = $offers;
        $this->offersType = $offersType;
        $this->sourceShowcaseLoansEntity = $sourceShowcaseLoansEntity;
    }

    private function getOffers(): array
    {
        if ($this->offersType === self::OFFER_TYPE_NEW) {
            return $this->prepareOffers();
        }
        return $this->prepareOldOffers();
    }

    private function prepareOffers(): array
    {
        return $this->offers->transform(fn(LoanOffer $loanOffer) => [
            'logo' =>  $loanOffer->loan->image_path,
            'siteName' => $loanOffer->loan->name,
            'rating' => str_replace('.', ',', $this->random_float(4.5, 5)),
            'sum' => number_format($loanOffer->loan->amount ?? 0, 0, ' ', ' ') . ' ₽',
            'percent' => $loanOffer->loan->issuing_bid,
            'duration' => $loanOffer->loan->issuing_period . ' дней',
            'license' => $loanOffer->loan->license,
            'offerUrl' => $loanOffer->getShowLink($this->sourceShowcaseLoansEntity->urlParameters) ?? '#',
        ])->toArray();
    }

    private function prepareOldOffers(): array
    {

        // Преобразуем сырые данные в более удобный формат для рендера
        $mapped = array_map(function ($offer) {
            return [
                'logo' => $offer['logo'] ?? '',
                'siteName' => $offer['site_name'] ?? '',
                'rating' => $offer['rating'] ?? 0.0,
                'sum' => number_format($offer['sum'] ?? 0, 0, ' ', ' ') . ' ₽',
                'percent' => $offer['extendedFields']['Условия кредитования']['Проценты по кредиту'] ?? 0.0,
                'duration' => ($offer['day'][1] ?? ($offer['day'][0] ?? 'Не указан')) . ' дней',
                'license' => $offer['extendedFields']['Лицензии']['Лицензия (N c датой)'] ?? 'Лицензия не указана',
                'offerUrl' => $offer['offerUrl'] ?? '#',
            ];
        }, $this->offers);
        return $mapped;
    }

    public function random_float($min, $max): float
    {
        return round($min + lcg_value() * (abs($max - $min)), 1, PHP_ROUND_HALF_UP);
    }


    public function render(): View
    {
        $offers = $this->getOffers();
        return view('components.offer-grid', [
            'data' => $offers
        ]);
    }
}
