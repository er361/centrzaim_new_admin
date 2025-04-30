<?php

namespace App\Services\OffersChecker;


use App\Jobs\ProcessOffersJob;
use Exception;
use Illuminate\Support\Facades\Log;

class OfferProcessingService
{
    public LeadsOffersChecker $leadsChecker;
    public ReportProcessor $reportProcessor;
    public UserOffersSyncer $userOffersSyncer;

    public function __construct(
        LeadsOffersChecker $leadsChecker,
        ReportProcessor $reportProcessor,
        UserOffersSyncer $userOffersSyncer
    ) {
        $this->leadsChecker = $leadsChecker;
        $this->reportProcessor = $reportProcessor;
        $this->userOffersSyncer = $userOffersSyncer;
    }

    /**
     * Высокоуровневый метод для процесса работы с предложениями.
     *
     * @param string $name
     * @param string $phone
     * @return array
     * @throws Exception
     */
    protected function processOffers(string $name, string $phone): array
    {
        $logger = Log::channel('offers');
        $logger->info('Processing offers', ['name' => $name, 'phone' => $phone]);

        // 1. Отправка запроса на проверку телефонов
        $reportId = $this->leadsChecker->checkPhones($name, [$phone]);

        // 2. Получение отчета
        $report = $this->leadsChecker->getReport($reportId);
        $logger->info('Report received', ['report' => $report]);

        return [
            'report_id' => $reportId,
            'status' => $report['status'],
            'report' => $report,
        ];
    }

    public function handle(string $name, string $phone): void
    {
        // Получаем report_id, но не проверяем статус
        $response = $this->processOffers($name, $phone);

        // Запускаем задачу для обработки статуса
        ProcessOffersJob::dispatch($name, $phone, $response['report_id'])->onQueue('offers');
    }
}