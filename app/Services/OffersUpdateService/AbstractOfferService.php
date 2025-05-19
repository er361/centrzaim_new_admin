<?php

namespace App\Services\OffersUpdateService;

use Illuminate\Support\Facades\Log;

/**
 * Abstract class implementing common functionality for offer services
 */
abstract class AbstractOfferService implements OfferServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function logDebug(string $message, array $context = []): void
    {
        $prefix = '[' . $this->getSourceName() . '] ';
        Log::channel('offers')->debug($prefix . $message, $context);
    }
    
    /**
     * {@inheritdoc}
     */
    public function logInfo(string $message, array $context = []): void
    {
        $prefix = '[' . $this->getSourceName() . '] ';
        Log::channel('offers')->info($prefix . $message, $context);
    }
    
    /**
     * {@inheritdoc}
     */
    public function logError(string $message, array $context = []): void
    {
        $prefix = '[' . $this->getSourceName() . '] ';
        Log::channel('offers')->error($prefix . $message, $context);
    }
    
    /**
     * {@inheritdoc}
     */
    public function updateOffers(): void
    {
        $this->logInfo('Starting offers update');
        
        $offers = $this->fetchOffers();
        
        if (!empty($offers)) {
            $this->logInfo('Successfully fetched ' . count($offers) . ' offers, proceeding to save');
            $this->saveOffers($offers);
        } else {
            $this->logError('No offers received or error occurred during fetch');
        }
        
        $this->logInfo('Finished offers update');
    }
}