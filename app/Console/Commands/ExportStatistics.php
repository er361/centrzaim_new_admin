<?php

namespace App\Console\Commands;

use App\Services\ReportService\RevenueReportStatisticsExportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class ExportStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:export {from} {to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Выгружает посчитанную статистику дохода за период во внешнее хранилище.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (!config('features.statistics_export.is_enabled')) {
            $this->info('Выгрузка статистики отключена.');
            return self::SUCCESS;
        }

        $startedAt = microtime(true);

        $from = Carbon::parse($this->argument('from'))->startOfDay();
        $to = Carbon::parse($this->argument('to'))->endOfDay();

        /** @var RevenueReportStatisticsExportService $exportService */
        $exportService = App::make(RevenueReportStatisticsExportService::class);

        while ($from->lte($to)) {
            $currentTo = $from->clone()->addWeek()->endOfDay()->min($to);

            $this->info('Выгружаем за период '.$from->toDateString().' -> '.$currentTo->toDateString());

            $exportService->export($from, $currentTo);

            $from->addWeek();
        }

        $completedIn = round(microtime(true) - $startedAt, 4);
        $this->info('Завершено за '.$completedIn.' секунд.');

        return Command::SUCCESS;
    }
}

