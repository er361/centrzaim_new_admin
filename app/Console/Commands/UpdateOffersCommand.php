<?php

namespace App\Console\Commands;

use App\DTO\Models\OfferApiModel;
use App\DTO\OfferDTO;
use App\Models\Loan;
use App\Services\OffersChecker\Settings;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateOffersCommand extends Command
{
    protected $signature = 'offers:update';

    protected $description = 'Обновляет офферы и сохраняет их в файл';

    public function handle(): void
    {
        Log::channel('offers')->info('Start update offers');
        $PLATFORM_ID = setting()->get('LEADS_PLATFORM_ID') ?? 1316606;

        $BASE_URL = 'https://api.leads.su/webmaster/offers/connectedPlatforms?';
        $EXTENDED_FIELDS = 1;
        $CATEGORIES = [14, 28];
        $TOKEN = 'de91b9234bbfd113de2171e70dcd343c';

        $categoriesString = implode(',', $CATEGORIES);

        $url = "{$BASE_URL}categories={$categoriesString}&limit=100&platform_id={$PLATFORM_ID}&extendedFields={$EXTENDED_FIELDS}&token={$TOKEN}";
        $response = Http::get($url);

        if ($response->ok()) {
            $data = $response->json();
            $offers = Arr::get($data, 'data', []);
            $this->saveOffers($offers);
//            $jsonData = json_encode($offers, JSON_UNESCAPED_UNICODE);
//            Storage::put('results.json', $jsonData);
        } else {
            Log::error('Error in getOffers ', ['message' => $response->body()]);
        }
        Log::channel('offers')->info('End update offers');
    }

    private function saveOffers(array $offers): void
    {
        $offerDTO = new OfferDTO($offers);
        collect($offerDTO->getOffers())->each(function (OfferApiModel $offer) {
            $loan = Loan::updateOrCreate(
                ['api_id' => $offer->id], // Критерии поиска
                [
                    'image_path' => $offer->image_path,
                    'name' => $offer->siteName,
                    'rating' => 0,
                    'amount' => $offer->summaZaima,
                    'issuing_period' => $offer->srok_zaima,
                    'issuing_bid' => $offer->percent,
                    'license' => $offer->license,
                    'description' => 'no desc',
                    'link' => $offer->link,
                    'api_id' => $offer->id,
                ]
            );

            $loan->loanLinks()->updateOrCreate(
                ['loan_id' => $loan->id], // loan_id должен ссылаться на ID модели Loan
                ['link' => $offer->link, 'source_id' => 1] // Остальные данные
            );

        });
    }
}
