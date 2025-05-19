<?php

namespace App\Services\OffersUpdateService;

use App\DTO\Models\OfferApiModel;
use App\DTO\OfferDTO;
use App\Models\Loan;
use App\Models\Source;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LeadsOfferService extends AbstractOfferService
{
    /**
     * The platform ID for Leads
     * 
     * @var int
     */
    private int $platformId;

    /**
     * The API token for Leads
     * 
     * @var string
     */
    private string $token;

    /**
     * The base URL for the Leads API
     * 
     * @var string
     */
    private string $baseUrl;

    /**
     * The categories to fetch offers for
     * 
     * @var array
     */
    private array $categories;

    /**
     * Flag to include extended fields
     * 
     * @var int
     */
    private int $extendedFields;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->platformId = setting()->get('LEADS_PLATFORM_ID') ?? 1316606;
        $this->token = 'de91b9234bbfd113de2171e70dcd343c';
        $this->baseUrl = 'https://api.leads.su/webmaster/offers/connectedPlatforms?';
        $this->categories = [14, 28]; // Loan categories
        $this->extendedFields = 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceId(): int
    {
        return Source::ID_LEADS;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSourceName(): string
    {
        return 'Leads';
    }

    /**
     * {@inheritdoc}
     */
    public function fetchOffers(): array
    {
        $this->logInfo('Fetching offers from API');
        
        $categoriesString = implode(',', $this->categories);
        $url = "{$this->baseUrl}categories={$categoriesString}&limit=100&platform_id={$this->platformId}&extendedFields={$this->extendedFields}&token={$this->token}";
        $this->logDebug('API request URL (token masked)', ['url' => str_replace($this->token, '***', $url)]);
        
        try {
            $response = Http::get($url);
            
            $this->logDebug('API response status', ['status' => $response->status()]);
            
            if ($response->ok()) {
                $data = $response->json();
                $offers = Arr::get($data, 'data', []);
                
                $this->logInfo('Successfully fetched offers', ['count' => count($offers)]);
                $this->logDebug('Offer counts and IDs', [
                    'count' => count($offers),
                    'first_ids' => !empty($offers) ? implode(', ', array_slice(array_column($offers, 'id'), 0, 3)) : 'No offers'
                ]);
                
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
            $offerDTO = new OfferDTO($offers);
            $transformedOffers = $offerDTO->getOffers();
            
            $this->logDebug('Transformed offers count', ['count' => count($transformedOffers)]);
            
            if (!empty($transformedOffers)) {
                $this->logDebug('Transformed offers stats', [
                    'count' => count($transformedOffers),
                    'first_ids' => implode(', ', array_slice(array_column($transformedOffers, 'id'), 0, 3))
                ]);
            }
            
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($transformedOffers as $index => $offer) {
                try {
                    $this->logDebug('Processing offer', [
                        'index' => $index,
                        'id' => $offer->id,
                        'name' => $offer->siteName
                    ]);
                    
                    $loanData = [
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
                        'source_id' => $this->getSourceId(),
                        'link_source_id' => $this->getSourceId(),
                    ];
                    
                    $this->logDebug('Processing loan', [
                        'api_id' => $loanData['api_id'],
                        'name' => $loanData['name']
                    ]);
                    
                    $loan = Loan::updateOrCreate(
                        ['api_id' => $offer->id],
                        $loanData
                    );
                    
                    $this->logDebug('Loan saved', [
                        'id' => $loan->id,
                        'is_new' => $loan->wasRecentlyCreated
                    ]);
                    
                    // Create or update the loan link
                    $loanLinkData = [
                        'link' => $offer->link,
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
                    
                    $successCount++;
                } catch (Throwable $e) {
                    $this->logError('Error saving offer', [
                        'index' => $index,
                        'offer_id' => $offer->id ?? 'unknown',
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    $errorCount++;
                }
            }
            
            $this->logInfo('Finished saving offers', [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'total_processed' => count($transformedOffers)
            ]);
        } catch (Throwable $e) {
            $this->logError('Exception during offer processing', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}