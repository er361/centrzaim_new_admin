<?php

namespace App\Console\Commands;

use App\Models\Webmaster;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteWebmaster extends Command
{
    protected $signature = 'webmaster:delete {webmaster_ids* : ID вебмастеров для удаления (можно несколько через пробел)} {--force : Принудительное удаление без подтверждения}';

    protected $description = 'Удаление вебмастера с обработкой внешних ключей';

    public function handle(): int
    {
        $webmasterIds = $this->argument('webmaster_ids');
        $force = $this->option('force');

        $webmasters = Webmaster::whereIn('id', $webmasterIds)->get();
        
        if ($webmasters->isEmpty()) {
            $this->error("Вебмастеры с указанными ID не найдены.");
            return self::FAILURE;
        }

        $notFoundIds = array_diff($webmasterIds, $webmasters->pluck('id')->toArray());
        if (!empty($notFoundIds)) {
            $this->warn("Не найдены вебмастеры с ID: " . implode(', ', $notFoundIds));
        }

        $this->info("Найдено вебмастеров для удаления: " . $webmasters->count());
        $this->table(['ID', 'API ID', 'Source ID'], $webmasters->map(function ($webmaster) {
            return [$webmaster->id, $webmaster->api_id, $webmaster->source_id];
        })->toArray());

        if (!$force && !$this->confirm('Вы уверены, что хотите удалить этих вебмастеров?')) {
            $this->info('Операция отменена.');
            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($webmasters) {
                foreach ($webmasters as $webmaster) {
                    $this->info("Обработка вебмастера ID: {$webmaster->id}");
                    
                    $usersCount = $webmaster->users()->count();
                    $conversionsCount = $webmaster->conversions()->count();
                    $actionsCount = $webmaster->actions()->count();
                    $loanOffersCount = $webmaster->loanOffers()->count();

                    if ($usersCount > 0) {
                        $this->warn("Найдено {$usersCount} пользователей, связанных с вебмастером ID {$webmaster->id}.");
                        
                        if (!$this->confirm("КРИТИЧНО! Подтвердите обнуление webmaster_id у {$usersCount} пользователей для вебмастера ID {$webmaster->id}?")) {
                            $this->error("Операция отменена пользователем для вебмастера ID {$webmaster->id}");
                            throw new \Exception("Отменено обнуление пользователей для вебмастера ID {$webmaster->id}");
                        }
                        
                        $webmaster->users()->update(['webmaster_id' => null]);
                        $this->info("Обнулен webmaster_id у {$usersCount} пользователей.");
                    }

                    if ($conversionsCount > 0) {
                        $this->warn("Найдено {$conversionsCount} конверсий, связанных с вебмастером ID {$webmaster->id}.");
                        $action = $this->choice(
                            "Что делать с конверсиями для вебмастера ID {$webmaster->id}?",
                            ['nullify' => 'Обнулить webmaster_id', 'delete' => 'Удалить'],
                            'nullify'
                        );
                        
                        if ($action === 'delete') {
                            $webmaster->conversions()->delete();
                            $this->info('Конверсии удалены.');
                        } else {
                            $webmaster->conversions()->update(['webmaster_id' => null]);
                            $this->info('webmaster_id обнулен у конверсий.');
                        }
                    }

                    if ($actionsCount > 0) {
                        $this->info("Удаление {$actionsCount} действий...");
                        $webmaster->actions()->delete();
                    }

                    if ($loanOffersCount > 0) {
                        $this->info("Удаление {$loanOffersCount} кредитных предложений...");
                        $webmaster->loanOffers()->delete();
                    }

                    $this->info('Удаление связей в pivot таблицах...');
                    DB::table('banner_webmaster')->where('webmaster_id', $webmaster->id)->delete();
                    DB::table('sms_included_webmaster')->where('webmaster_id', $webmaster->id)->delete();
                    DB::table('sms_excluded_webmaster')->where('webmaster_id', $webmaster->id)->delete();
                    DB::table('user_accessible_webmaster')->where('webmaster_id', $webmaster->id)->delete();
                    DB::table('webmaster_templates')->where('webmaster_id', $webmaster->id)->delete();

                    $this->info("Удаление вебмастера ID {$webmaster->id}...");
                    $webmaster->delete();

                    $this->info("Вебмастер с ID {$webmaster->id} успешно удален.");
                }
            });

            $this->info("Все вебмастеры успешно удалены.");
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Ошибка при удалении вебмастеров: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
