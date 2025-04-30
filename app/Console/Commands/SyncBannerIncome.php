<?php

namespace App\Console\Commands;

use App\Services\BannerService\AdsFinSyncStatisticsService;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

class SyncBannerIncome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banner:sync-income';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизация дохода с баннеров.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        /** @var AdsFinSyncStatisticsService $adsFinService */
        $adsFinService = App::make(AdsFinSyncStatisticsService::class);
        $adsFinService->syncIncome();

        return self::SUCCESS;
    }
}
