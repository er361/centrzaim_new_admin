<?php

namespace App\Console\Commands;

use App\Services\ReportService\RevenueReportStatisticsExportService;
use App\Services\ReportService\RevenueReportStatisticsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class ComputeStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:compute {from?} {to?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Считает статистику дохода.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $startedAt = microtime(true);

        $from = $this->argument('from')
            ? Carbon::parse($this->argument('from'))->startOfDay()
            : Carbon::yesterday()->startOfDay();
        $to = $this->argument('to')
            ? Carbon::parse($this->argument('to'))->endOfDay()
            : Carbon::now();

        /** @var RevenueReportStatisticsExportService $exportService */
        $exportService = App::make(RevenueReportStatisticsExportService::class);

        while ($from->lte($to)) {
            $currentTo = $from->clone()->addWeek()->endOfDay()->min($to);

            $this->info('Считаем за период ' . $from->toDateString() . ' -> ' . $currentTo->toDateString());

            $service = new RevenueReportStatisticsService($from, $currentTo);
            $service->update();

            $exportService->export($from, $currentTo);

            $from->addWeek();
        }

        $completedIn = round(microtime(true) - $startedAt, 4);
        $this->info('Завершено за ' . $completedIn . ' секунд.');

        return Command::SUCCESS;
    }
}

