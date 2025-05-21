<?php

namespace App\Services;

use App\Models\LoanOffer;
use App\Models\Showcase;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use App\Repositories\LoanOfferRepository;
use App\Services\OffersChecker\Settings;
use Hamcrest\Core\Set;
use Illuminate\Support\Collection;


class UserOfferService
{
    private int $mainOffersCount;

    public function __construct()
    {
        $this->mainOffersCount = config('VITRINA.MAIN_OFFERS_COUNT');
    }

//    const OFFER_SUMM_FIELD = 'Cумма для микро и кред. карт';
    const OFFER_SUMM_FIELD = 'Сумма кредита до';

    /**
     * @param Showcase $showcase
     * @param Source|null $source
     * @param Webmaster|null $webmaster
     * @param User|null $user
     * @param LoanOfferRepository|null $loanOfferRepository
     * @return LoanOffer[]| Collection
     */
    public function getOffersNew(
        Showcase            $showcase,
        ?Source              $source,
        ?Webmaster           $webmaster,
        ?User                $user,
        ?LoanOfferRepository $loanOfferRepository = new LoanOfferRepository()
    ): array | Collection
    {
        $offers = $loanOfferRepository->getPageLoans($webmaster, $showcase, $source)
            ->whereIsBackup(false)
            ->get();

        $offers = $this->filterOffers($user, $offers);

        $offers = $this->addBackupItems($offers, $loanOfferRepository, $webmaster, $showcase, $source);

        return $offers;
    }

    /**
     * @param User|null $user
     * @return array
     * @deprecated Use getOffersNew new method
     */
    public function getOffers(User $user = null): array
    {
        $offers = Settings::getOffers();
        if ($user) {

            $user->load('offers');

            if ($user->offers) {
                $offers = array_filter($offers, fn($offer) => !in_array($offer['id'], $user->offers->repeated_offers));
            }
        }

        $offers = collect($offers)->filter(fn($offer) => in_array($offer['id'], $user ? Settings::$OFFER_IDS : Settings::$PUBLIC_OFFERS_IDS))->toArray();

//        Settings::setOffersPosition($offers, Settings::$OFFER_IDS);

        // Перенос логики из представления
        foreach ($offers as &$offer) {
            $offer['offerUrl'] = Settings::getOfferUrl(Settings::getPlatformId(), $offer['tracking_urls']);
            $sum = data_get($offer, 'extendedFields.Условия кредитования.' . self::OFFER_SUMM_FIELD, '0');

            $offer['sum'] = $sum;
            $offer['day'] = isset($offer['extendedFields']['Условия кредитования']['Срок для микро и кред. карт'])
                ? explode('-', $offer['extendedFields']['Условия кредитования']['Срок для микро и кред. карт'])
                : [0, 0];
            $offer['rating'] = str_replace('.', ',', $this->random_float(4.5, 5));
        }
        return $offers;
    }

    public function random_float($min, $max): float
    {
        return round($min + lcg_value() * (abs($max - $min)), 1, PHP_ROUND_HALF_UP);
    }

    /**
     * @param User|null $user
     * @param array|\Illuminate\Database\Eloquent\Collection|\LaravelIdea\Helper\App\Models\_IH_LoanOffer_C $offers
     * @return array|\Illuminate\Database\Eloquent\Collection|Collection|\LaravelIdea\Helper\App\Models\_IH_LoanOffer_C
     */
    public function filterOffers(?User $user, array|\Illuminate\Database\Eloquent\Collection|\LaravelIdea\Helper\App\Models\_IH_LoanOffer_C $offers): \LaravelIdea\Helper\App\Models\_IH_LoanOffer_C|Collection|array|\Illuminate\Database\Eloquent\Collection
    {
        if ($user) {

            $user->load('offers');

            if ($user->offers) {
                $offers = $offers->filter(fn(LoanOffer $offer) => !in_array($offer->loan->api_id, $user->offers->repeated_offers));
            }
        }
        return $offers;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection|array|Collection|\LaravelIdea\Helper\App\Models\_IH_LoanOffer_C $offers
     * @param LoanOfferRepository|null $loanOfferRepository
     * @param Webmaster|null $webmaster
     * @param Showcase $showcase
     * @param Source|null $source
     * @return array|\Illuminate\Database\Eloquent\Collection|Collection|\LaravelIdea\Helper\App\Models\_IH_LoanOffer_C
     */
    public function addBackupItems(\Illuminate\Database\Eloquent\Collection|array|Collection|\LaravelIdea\Helper\App\Models\_IH_LoanOffer_C $offers, ?LoanOfferRepository $loanOfferRepository, ?Webmaster $webmaster, Showcase $showcase, ?Source $source): \LaravelIdea\Helper\App\Models\_IH_LoanOffer_C|Collection|array|\Illuminate\Database\Eloquent\Collection
    {
        if ($offers->count() < $this->mainOffersCount) {
            $backUpOffers = $loanOfferRepository->getPageLoans($webmaster, $showcase, $source)
                ->whereIsBackup(true)
                ->limit($this->mainOffersCount - $offers->count())
                ->get();
            $offers = $offers->merge($backUpOffers);
        }
        return $offers;
    }
}