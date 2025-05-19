<?php

namespace App\Services\OffersUpdateService;

use App\Models\Source;
use InvalidArgumentException;

/**
 * Factory for creating offer services
 */
class OfferServiceFactory
{
    /**
     * Get an offer service for the specified source ID
     * 
     * @param int $sourceId The source ID to get a service for
     * @return OfferServiceInterface The offer service
     * @throws InvalidArgumentException If no service is available for the source ID
     */
    public static function getService(int $sourceId): OfferServiceInterface
    {
        return match ($sourceId) {
            Source::ID_LEADS => new LeadsOfferService(),
            Source::ID_RAFINAD => new RafinadOfferService(),
            // Add more sources here as they are implemented
            // Example: Source::ID_GURU_LEADS => new GuruLeadsOfferService(),
            default => throw new InvalidArgumentException("No offer service available for source ID: {$sourceId}")
        };
    }

    /**
     * Get all available offer services
     * 
     * @return array<OfferServiceInterface>
     */
    public static function getAllServices(): array
    {
        return [
            new LeadsOfferService(),
            new RafinadOfferService(),
            // Add more services here as they are implemented
        ];
    }

    /**
     * Get the default offer service
     * 
     * @return OfferServiceInterface
     */
    public static function getDefaultService(): OfferServiceInterface
    {
        return new LeadsOfferService();
    }
}