<?php

namespace App\Jobs;

use App\Services\OffersChecker\OfferProcessingService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOffersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private string $name;
    private string $phone;
    private int $reportId;

    public int $tries = 5; // Количество попыток
    public int $backoff = 75; // Задержка между попытками (2 минуты)

    public function __construct(string $name, string $phone, int $reportId)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->reportId = $reportId;
    }

    public function handle(OfferProcessingService $service): void
    {
        $logger = Log::channel('offers');
        $logger->info('Checking report status', ['report_id' => $this->reportId]);

        // Получаем отчет
        $report = $service->leadsChecker->getReport($this->reportId);

        // Проверяем статус
        if ($report['status'] === 'processing') {
            $logger->info('Report still processing, re-dispatching job', ['report' => $report]);
            throw new Exception('Report still processing'); // Job повторится автоматически
        }

        if ($report['status'] === 'unprocessed') {
            $logger->info('Report unprocessed, stopping job', ['report' => $report]);
            return;
        }

        // Если статус готов, продолжаем обработку
        $offers = $service->reportProcessor->extractOffers($report);
        $service->userOffersSyncer->syncOffers($this->phone, $offers);

        $logger->info('Offers processed successfully', ['phone' => $this->phone]);
    }

    public function failed(): void
    {
        Log::channel('offers')->error('Job failed after max attempts', [
            'name' => $this->name,
            'phone' => $this->phone,
            'report_id' => $this->reportId,
        ]);
    }
}
