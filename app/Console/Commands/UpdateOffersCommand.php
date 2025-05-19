<?php

namespace App\Console\Commands;

use App\Services\OffersUpdateService\OfferServiceFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateOffersCommand extends Command
{
    protected $signature = 'offers:update {source?}';

    protected $description = 'Обновляет офферы из указанного источника или из всех источников';

    public function handle(): void
    {
        Log::channel('offers')->info('Starting offer update command');
        
        $sourceId = $this->argument('source');
        
        try {
            if ($sourceId !== null) {
                // Update offers from a specific source
                try {
                    $sourceId = (int) $sourceId;
                    Log::channel('offers')->info("Updating offers from specific source", ['source_id' => $sourceId]);
                    $this->updateSourceOffers($sourceId);
                } catch (Throwable $e) {
                    $this->error($e->getMessage());
                    Log::channel('offers')->error('Error updating offers from specific source', [
                        'source_id' => $sourceId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                // Update offers from all available sources
                Log::channel('offers')->info("Updating offers from all available sources");
                $this->updateAllOffers();
            }
        } catch (Throwable $e) {
            $this->error("Unexpected error: " . $e->getMessage());
            Log::channel('offers')->error('Unexpected error in command', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        Log::channel('offers')->info('Finished offer update command');
    }

    /**
     * Update offers from a specific source
     * 
     * @param int $sourceId The source ID
     * @return void
     */
    private function updateSourceOffers(int $sourceId): void
    {
        try {
            $service = OfferServiceFactory::getService($sourceId);
            $this->info("Updating offers from source ID: {$sourceId}");
            
            Log::channel('offers')->debug("Starting update for source", [
                'source_id' => $sourceId,
                'source_class' => get_class($service)
            ]);
            
            $service->updateOffers();
            
            $this->info("Offers from source ID: {$sourceId} updated successfully");
            Log::channel('offers')->info("Successfully updated offers for source", ['source_id' => $sourceId]);
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            Log::channel('offers')->error('Error getting or running service for source', [
                'source_id' => $sourceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Update offers from all available sources
     * 
     * @return void
     */
    private function updateAllOffers(): void
    {
        $services = OfferServiceFactory::getAllServices();
        $sourceCount = count($services);
        
        Log::channel('offers')->info("Found services for sources", ['count' => $sourceCount]);
        
        $successCount = 0;
        $failureCount = 0;
        
        foreach ($services as $index => $service) {
            $sourceId = $service->getSourceId();
            
            try {
                $currentIndex = $index + 1;
                $this->info("Updating offers from source ID: {$sourceId} ({$currentIndex}/{$sourceCount})");
                
                Log::channel('offers')->debug("Starting update for source in batch", [
                    'source_id' => $sourceId,
                    'source_class' => get_class($service),
                    'index' => $currentIndex,
                    'total' => $sourceCount
                ]);
                
                $service->updateOffers();
                
                $this->info("Offers from source ID: {$sourceId} updated successfully");
                Log::channel('offers')->info("Successfully updated offers for source in batch", [
                    'source_id' => $sourceId,
                    'index' => $currentIndex,
                    'total' => $sourceCount
                ]);
                
                $successCount++;
            } catch (Throwable $e) {
                $this->error("Error updating source ID: {$sourceId}: " . $e->getMessage());
                Log::channel('offers')->error('Error updating source in batch', [
                    'source_id' => $sourceId,
                    'index' => $index + 1,
                    'total' => $sourceCount,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $failureCount++;
            }
        }
        
        Log::channel('offers')->info("Completed updating all sources", [
            'total' => $sourceCount,
            'success' => $successCount,
            'failure' => $failureCount
        ]);
    }
}