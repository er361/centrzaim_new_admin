<?php

namespace App\Services\OffersUpdateService;

use App\Models\Loan;
use App\Models\Source;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RafinadOfferService extends AbstractOfferService
{
    /**
     * The API token for Rafinad
     * 
     * @var string
     */
    private string $token;

    /**
     * The base URL for the Rafinad API
     * 
     * @var string
     */
    private string $baseUrl;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->token = setting()->get('RAFINAD_TOKEN') ?? '311fb7d764a10cb932c4b723a2ce948b5e31b717';
        $this->baseUrl = 'https://rafinad.io/api/v1/me/offers/webmaster/';
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceId(): int
    {
        return Source::ID_RAFINAD;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSourceName(): string
    {
        return 'Rafinad';
    }

    /**
     * {@inheritdoc}
     */
    public function fetchOffers(): array
    {
        $this->logInfo('Fetching offers from API');
        
        $this->logDebug('API request URL', ['url' => $this->baseUrl]);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->token
            ])->get($this->baseUrl);
            
            $this->logDebug('API response status', ['status' => $response->status()]);
            
            if ($response->ok()) {
                $data = $response->json();
                $this->logDebug('API response received', [
                    'status' => 'success',
                    'has_results' => isset($data['results']),
                    'count' => isset($data['results']) ? count($data['results']) : 0
                ]);
                
                $offers = $data['results'] ?? [];
                
                $this->logInfo('Successfully fetched offers', ['count' => count($offers)]);
                
                if (!empty($offers)) {
                    $this->logDebug('Offer IDs', [
                        'first_ids' => implode(', ', array_slice(array_column($offers, 'id'), 0, 3))
                    ]);
                }
                
                return $offers;
            } else {
                $this->logError('API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return [];
            }
        } catch (Throwable $e) {
            $this->logError('Exception during API request', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveOffers(array $offers): void
    {
        $this->logInfo('Processing and saving offers', ['count' => count($offers)]);
        
        try {
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($offers as $index => $offer) {
                try {
                    $this->logDebug('Processing offer', [
                        'index' => $index,
                        'id' => $offer['id'] ?? 'unknown',
                        'name' => $offer['name'] ?? 'unknown'
                    ]);
                    
                    // Calculate loan amount from the description
                    $amount = $this->extractAmount($offer['description'] ?? '');
                    $this->logDebug('Loan amount', ['amount' => $amount]);
                    
                    // Extract period from description 
                    $period = $this->extractPeriod($offer['description'] ?? '');
                    $this->logDebug('Loan period', ['period' => $period]);
                    
                    // Get the price for the offer (if available)
                    $price = $this->getOfferPrice($offer);
                    $this->logDebug('Loan price', ['price' => $price]);
                    
                    // Get the link from the first flow's lending_url if available
                    $link = '';
                    if (isset($offer['flows'][0]['lending_urls'][0]['url'])) {
                        $link = $offer['flows'][0]['lending_urls'][0]['url'];
                    }
                    $this->logDebug('Loan link', ['has_link' => !empty($link)]);
                    
                    // Use just the numeric ID for the database (api_id is an integer column)
                    $apiId = (int) $offer['id'];
                    
                    // Create or update the loan record
                    $loanData = [
                        'image_path' => $offer['image'] ?? '',
                        'name' => $offer['name'] ?? '',
                        'rating' => 0,
                        'license' => '',
                        'description' => 'no description',
                        'link' => $link,
                        'api_id' => $apiId,
                        'source_id' => $this->getSourceId(),
                        'link_source_id' => $this->getSourceId(),
                    ];
                    
                    $this->logDebug('Processing loan', [
                        'api_id' => $apiId,
                        'name' => $loanData['name']
                    ]);
                    
                    $loan = Loan::updateOrCreate(
                        ['api_id' => $apiId],
                        $loanData
                    );
                    
                    // Set API fields using setters that respect immutable_fields
                    $loan->setApiAmountWith($amount);
                    $loan->setApiIssuingPeriodWith($period);
                    $loan->setApiIssuingBidWith($price);
                    $loan->save();
                    
                    $this->logDebug('Loan saved', [
                        'id' => $loan->id,
                        'is_new' => $loan->wasRecentlyCreated
                    ]);
                    
                    // Create or update the loan link if link is not empty
                    if (!empty($link)) {
                        $loanLinkData = [
                            'link' => $link,
                            'source_id' => $this->getSourceId()
                        ];
                        
                        $this->logDebug('Creating/updating loan link', [
                            'loan_id' => $loan->id
                        ]);
                        
                        $loanLink = $loan->loanLinks()->updateOrCreate(
                            ['loan_id' => $loan->id],
                            $loanLinkData
                        );
                        
                        $this->logDebug('Loan link saved', [
                            'id' => $loanLink->id,
                            'is_new' => $loanLink->wasRecentlyCreated
                        ]);
                    } else {
                        $this->logDebug('Skipping loan link creation - empty link');
                    }
                    
                    $successCount++;
                } catch (Throwable $e) {
                    $this->logError('Error saving offer', [
                        'index' => $index,
                        'offer_id' => $offer['id'] ?? 'unknown',
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    $errorCount++;
                }
            }
            
            $this->logInfo('Finished saving offers', [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'total_processed' => count($offers)
            ]);
        } catch (Throwable $e) {
            $this->logError('Exception during offer processing', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Extract the loan amount from the description
     * 
     * @param string $description
     * @return string
     */
    private function extractAmount(string $description): string
    {
        $amount = '0';
        
        // Try to extract amount from description like "Сумма займа: от 1 000 до 200 000 рублей"
        if (preg_match('/Сумма займа: от [\d\s]+ до ([\d\s]+) рублей/i', $description, $matches)) {
            $amount = str_replace(' ', '', $matches[1]);
            $this->logDebug('Extracted amount using pattern 1', ['amount' => $amount]);
        } elseif (preg_match('/от [\d\s]+ до ([\d\s]+) рублей/i', $description, $matches)) {
            $amount = str_replace(' ', '', $matches[1]);
            $this->logDebug('Extracted amount using pattern 2', ['amount' => $amount]);
        } elseif (preg_match('/от [\d\s]+ до ([\d\s]+) руб/i', $description, $matches)) {
            $amount = str_replace(' ', '', $matches[1]);
            $this->logDebug('Extracted amount using pattern 3', ['amount' => $amount]);
        } else {
            $this->logDebug('Could not extract amount');
        }
        
        return $amount;
    }
    
    /**
     * Extract the loan period from the description
     * 
     * @param string $description
     * @return string
     */
    private function extractPeriod(string $description): string
    {
        $period = '0';
        
        // Try to extract period from description like "Срок займа от 3 до 180 дней"
        if (preg_match('/Срок займа от [\d\s]+ до ([\d\s]+) дней/i', $description, $matches)) {
            $period = str_replace(' ', '', $matches[1]);
            $this->logDebug('Extracted period using pattern 1', ['period' => $period]);
        } elseif (preg_match('/от [\d\s]+ до ([\d\s]+) дней/i', $description, $matches)) {
            $period = str_replace(' ', '', $matches[1]);
            $this->logDebug('Extracted period using pattern 2', ['period' => $period]);
        } else {
            $this->logDebug('Could not extract period');
        }
        
        return $period;
    }
    
    /**
     * Get the price for an offer (if available)
     * 
     * @param array $offer
     * @return string
     */
    private function getOfferPrice(array $offer): string
    {
        $price = '0';
        
        // Try to get the price from the first action if available
        if (isset($offer['actions'][0]['prices'][0]['amount'])) {
            $price = (string) $offer['actions'][0]['prices'][0]['amount'];
            $this->logDebug('Extracted price', ['price' => $price]);
        } else {
            $this->logDebug('Could not extract price');
        }
        
        return $price;
    }
}