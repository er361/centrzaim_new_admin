<?php

namespace App\Services\OffersUpdateService;

/**
 * Interface OfferServiceInterface
 * 
 * Defines contract for offer services that fetch and process offers from different sources
 */
interface OfferServiceInterface
{
    /**
     * Fetch offers from the source
     * 
     * @return array The raw offers data
     */
    public function fetchOffers(): array;
    
    /**
     * Process and save offers to the database
     * 
     * @param array $offers The offers to process and save
     * @return void
     */
    public function saveOffers(array $offers): void;
    
    /**
     * Update all offers from the source
     * 
     * @return void
     */
    public function updateOffers(): void;
    
    /**
     * Get the source ID that this service is responsible for
     * 
     * @return int
     */
    public function getSourceId(): int;
    
    /**
     * Get source name for logging purposes
     * 
     * @return string
     */
    public function getSourceName(): string;
    
    /**
     * Log debug information
     * 
     * @param string $message The message to log
     * @param array $context Additional context data
     * @return void
     */
    public function logDebug(string $message, array $context = []): void;
    
    /**
     * Log info level information
     * 
     * @param string $message The message to log
     * @param array $context Additional context data
     * @return void
     */
    public function logInfo(string $message, array $context = []): void;
    
    /**
     * Log error information
     * 
     * @param string $message The message to log
     * @param array $context Additional context data
     * @return void
     */
    public function logError(string $message, array $context = []): void;
}