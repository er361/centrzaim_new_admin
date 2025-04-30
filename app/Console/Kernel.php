<?php

namespace App\Console;

use App\Console\Commands\ComputeStatistics;
use App\Console\Commands\CreateRecurrentPayments;
use App\Console\Commands\LoadSmsUserStatus;
use App\Console\Commands\SendDefaultSms;
use App\Console\Commands\SendNoCardSms;
use App\Console\Commands\SendUsersToLeadServices;
use App\Console\Commands\SyncBannerIncome;
use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Process;
use Spatie\Backup\Commands\BackupCommand;
use Spatie\Backup\Commands\CleanupCommand;
use Spatie\Backup\Commands\MonitorCommand;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $this->scheduleBackups($schedule);
        $this->scheduleSms($schedule);

        $schedule->command(CreateRecurrentPayments::class)
            ->everyTwoMinutes()
            ->withoutOverlapping(6 * 60) // 6 часов
            ->runInBackground();

        $schedule->command(SendUsersToLeadServices::class)
            ->everyFifteenMinutes()
            ->withoutOverlapping()
            ->runInBackground();

//        $schedule->command(SyncBannerIncome::class)
//            ->everyFifteenMinutes()
//            ->withoutOverlapping()
//            ->runInBackground();

        $schedule->command(ComputeStatistics::class)
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command(PruneStaleTagsCommand::class)
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command('telescope:prune --hours=48')->daily();
        $schedule->command('offers:update')->daily();
        $schedule->command('sms:update-from-name')->daily()->at('06:00');

        $schedule->command('every:try-day-charge')->daily()->at('12:00');

        $schedule->call(function () {
            Process::run('rm -f ../storage/logs/cron.log');
        })->daily();
    }

    protected function scheduleBackups(Schedule $schedule): void
    {
        $schedule->command(BackupCommand::class)
            ->twiceDaily(9, 21)
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command(CleanupCommand::class)
            ->dailyAt('10:00')
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command(MonitorCommand::class)
            ->dailyAt('11:00')
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Отправка SMS.
     * @param  Schedule  $schedule
     * @return void
     */
    protected function scheduleSms(Schedule $schedule): void
    {
        $schedule->command(SendDefaultSms::class)
            ->everyFourMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command(SendNoCardSms::class)
            ->everyFourMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command(LoadSmsUserStatus::class)
            ->everyTenMinutes()
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
//        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
