<?php

namespace App\Services\ReportService;

use App\Models\Statistic;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelWriter;

class RevenueReportStatisticsExportService
{
    /**
     * Выгружает статистику за период во внешнее хранилище.
     *
     * @param CarbonInterface $dateFrom Начало периода.
     * @param CarbonInterface $dateTo Конец периода.
     *
     * @return void
     */
    public function export(CarbonInterface $dateFrom, CarbonInterface $dateTo): void
    {
        $statisticsFilePath = 'tmp/statistics_export.csv';

        if (Storage::exists($statisticsFilePath)) {
            Storage::delete($statisticsFilePath);
        }

        $statisticsFile = SimpleExcelWriter::create(Storage::path($statisticsFilePath));

        Statistic::query()
            ->with([
                'webmaster',
            ])
            ->where('date', '>=', $dateFrom)
            ->where('date', '<=', $dateTo)
            ->eachById(function (Statistic $statistic) use ($statisticsFile) {
                $row = $statistic->withoutRelations()->toArray();

                $webmasterApiId = $statistic->webmaster?->api_id ?? null;

                unset($row['webmaster_id']);
                unset($row['created_at']);
                unset($row['updated_at']);
                unset($row['version']);
                unset($row['id']);

                $row['date'] = Carbon::parse($row['date'])
                    ->setTimezone(config('app.timezone'))
                    ->toDateString();
                $row['webmaster_api_id'] = $webmasterApiId;

                $statisticsFile->addRow($row);
            }, 1000);

        $statisticsFile->close();

        $exportDirectory = 'exports/' . config('postbacks.site_id');
        $exportFilePrefix = Carbon::now()->format('Ymd_Hisu');

        Storage::disk('stats')->writeStream(
            $exportDirectory . '/statistics_' . $exportFilePrefix . '.csv',
            Storage::readStream($statisticsFilePath)
        );

        Storage::delete($statisticsFilePath);
    }
}