<?php

namespace App\Jobs;

use App\Services\ReportService\RevenueReportStatisticsExportService;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportStatistics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 240;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected CarbonInterface $dateFrom,
        protected CarbonInterface $dateTo
    )
    {
        // Nothing
    }

    /**
     * Execute the job.
     *
     * @param RevenueReportStatisticsExportService $exportService
     * @return void
     */
    public function handle(RevenueReportStatisticsExportService $exportService): void
    {
        $exportService->export($this->dateFrom, $this->dateTo);
    }
}
