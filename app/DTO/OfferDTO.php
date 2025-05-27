<?php

namespace App\DTO;

use App\DTO\Models\OfferApiModel;
use App\Services\OffersChecker\Settings;
use Illuminate\Support\Collection;

class OfferDTO
{
    const string OFFER_AMOUNT_FIELD = 'Сумма кредита до';
    const string OFFER_PERIOD_FIELD = 'Срок кредитования до';
    private array $offers;

    public function __construct(array $offers)
    {
        $this->offers = $offers;
    }

    /**
     * @return OfferApiModel []
     */
    public function getOffers(): array
    {
        return $this->transform();
    }


    /**
     * @return OfferApiModel []
     */
    private function transform(): array
    {

        return collect($this->offers)->transform(function ($offer) {
            $sum = data_get($offer, 'extendedFields.Условия кредитования.' . self::OFFER_AMOUNT_FIELD, '0');
            $offerUrl = Settings::getOfferUrl(Settings::getPlatformId(), $offer['tracking_urls']);

            $srokZaima = data_get($offer, 'extendedFields.Условия кредитования.'.self::OFFER_PERIOD_FIELD, '0');

            $license = $offer['extendedFields']['Лицензии']['Лицензия (N c датой)'] ?? 'Лицензия не указана';
            $percent = $offer['extendedFields']['Условия кредитования']['Проценты по кредиту'] ?? 0.0;

            $apiModel = new OfferApiModel();
            $apiModel->image_path = $offer['logo'];
            $apiModel->siteName = $offer['site_name'];
            $apiModel->rating = str_replace('.', ',', $this->random_float(4.5, 5));
            $apiModel->summaZaima = $sum;
            $apiModel->percent = $percent;
            $apiModel->srok_zaima = $srokZaima;
            $apiModel->license = $license;
            $apiModel->link = $offerUrl;
            $apiModel->id = $offer['id'];

            return $apiModel;
        })->toArray();
    }

    public function random_float($min, $max): float
    {
        return round($min + lcg_value() * (abs($max - $min)), 1, PHP_ROUND_HALF_UP);
    }

}
