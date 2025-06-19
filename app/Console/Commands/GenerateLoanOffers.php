<?php

namespace App\Console\Commands;

use App\Models\LoanOffer;
use App\Models\Showcase;
use App\Models\Source;
use App\Models\Loan;
use App\Models\LoanLink;
use App\Models\Webmaster;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateLoanOffers extends Command
{
    protected $signature = 'showcase:generate-offers 
                            {showcase_id : ID витрины для генерации офферов}
                            {--source= : ID источника для фильтрации офферов}
                            {--user_view_source= : ID источника для записи в source_id офферов}
                            {--webmaster= : ID вебмастера (опционально)}
                            {--limit=50 : Максимальное количество офферов для генерации}
                            {--priority-start=1 : Начальный приоритет для офферов}
                            {--clear : Очистить существующие офферы перед генерацией}';

    protected $description = 'Генерация офферов займов для витрины из указанных источников';

    public function handle(): int
    {
        $showcaseId = $this->argument('showcase_id');
        $sourceIds = $this->option('source');
        $userViewSourceId = $this->option('user_view_source');
        $webmasterId = $this->option('webmaster');
        $limit = (int) $this->option('limit');
        $priorityStart = (int) $this->option('priority-start');
        $clear = $this->option('clear');

        $showcase = Showcase::find($showcaseId);
        if (!$showcase) {
            $this->error("Витрина с ID {$showcaseId} не найдена.");
            return self::FAILURE;
        }

        $this->info("Витрина: {$showcase->name} (ID: {$showcase->id})");

        // Запрос подтверждения если не local окружение
        if (app()->environment() !== 'local') {
            if (!$this->confirm('Вы работаете в ' . app()->environment() . ' окружении. Продолжить генерацию офферов?')) {
                $this->info('Операция отменена.');
                return self::SUCCESS;
            }
        }

        if (empty($sourceIds)) {
            $sources = Source::all();
            $this->info("Источники не указаны, будут использованы все доступные источники.");
        } else {
            $sourceIds = is_string($sourceIds) ? explode(',', $sourceIds) : (array) $sourceIds;
            $sources = Source::whereIn('id', $sourceIds)->get();
            if ($sources->isEmpty()) {
                $this->error("Источники с указанными ID не найдены.");
                return self::FAILURE;
            }
        }

        $this->info("Найдено источников: " . $sources->count());
        $this->table(['ID', 'Название'], $sources->map(function ($source) {
            return [$source->id, $source->name];
        })->toArray());

        if ($webmasterId) {
            $webmaster = Webmaster::find($webmasterId);
            if (!$webmaster) {
                $this->error("Вебмастер с ID {$webmasterId} не найден.");
                return self::FAILURE;
            }
            $this->info("Вебмастер: {$webmaster->api_id} (ID: {$webmaster->id})");
        }

        if ($clear) {
            $existingCount = LoanOffer::where('showcase_id', $showcaseId)->count();
            if ($existingCount > 0) {
                if (!$this->confirm("Удалить {$existingCount} существующих офферов для витрины {$showcase->name}?")) {
                    $this->info('Операция отменена.');
                    return self::SUCCESS;
                }
            }
        }

        try {
            DB::transaction(function () use ($showcase, $sources, $webmasterId, $limit, $priorityStart, $clear, $userViewSourceId) {
                if ($clear) {
                    $deletedCount = LoanOffer::where('showcase_id', $showcase->id)->delete();
                    $this->info("Удалено {$deletedCount} существующих офферов.");
                }

                $currentPriority = $priorityStart;
                $totalGenerated = 0;

                foreach ($sources as $source) {
                    $this->info("Обработка источника: {$source->name}");

                    $loanLinksQuery = LoanLink::where('source_id', $source->id)
                        ->whereHas('loan')
                        ->with('loan');

                    if ($webmasterId) {
                        $loanLinksQuery->where('webmaster_id', $webmasterId);
                    }

                    $loanLinks = $loanLinksQuery->take($limit)->get();

                    if ($loanLinks->isEmpty()) {
                        $this->warn("Для источника {$source->name} не найдено подходящих ссылок займов.");
                        continue;
                    }

                    foreach ($loanLinks as $loanLink) {
                        if ($totalGenerated >= $limit) {
                            $this->info("Достигнут лимит в {$limit} офферов.");
                            break 2;
                        }

                        $offerSourceId = $userViewSourceId ?? $source->id;
                        
                        $existingOffer = LoanOffer::where([
                            'showcase_id' => $showcase->id,
                            'source_id' => $offerSourceId,
                            'loan_id' => $loanLink->loan_id,
                            'webmaster_id' => $webmasterId,
                        ])->first();

                        if ($existingOffer) {
                            $this->warn("Оффер для займа {$loanLink->loan->name} уже существует, пропускаем.");
                            continue;
                        }

                        LoanOffer::create([
                            'priority' => $currentPriority,
                            'showcase_id' => $showcase->id,
                            'source_id' => $offerSourceId,
                            'loan_link_id' => $loanLink->id,
                            'loan_id' => $loanLink->loan_id,
                            'webmaster_id' => $webmasterId,
                            'is_hidden' => false,
                            'is_backup' => false,
                        ]);

                        $this->line("Создан оффер: {$loanLink->loan->name} (приоритет: {$currentPriority})");
                        $currentPriority++;
                        $totalGenerated++;
                    }
                }

                $this->info("Всего сгенерировано офферов: {$totalGenerated}");
            });

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Ошибка при генерации офферов: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
