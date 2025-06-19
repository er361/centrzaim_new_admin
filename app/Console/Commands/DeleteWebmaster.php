<?php

namespace App\Console\Commands;

use App\Models\Webmaster;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteWebmaster extends Command
{
    protected $signature = 'webmaster:delete {webmaster_id : ID вебмастера для удаления} {--force : Принудительное удаление без подтверждения}';

    protected $description = 'Удаление вебмастера с обработкой внешних ключей';

    public function handle(): int
    {
        $webmasterId = $this->argument('webmaster_id');
        $force = $this->option('force');

        $webmaster = Webmaster::find($webmasterId);
        
        if (!$webmaster) {
            $this->error("Вебмастер с ID {$webmasterId} не найден.");
            return self::FAILURE;
        }

        $this->info("Найден вебмастер: ID {$webmaster->id}, API ID: {$webmaster->api_id}");

        if (!$force && !$this->confirm('Вы уверены, что хотите удалить этого вебмастера?')) {
            $this->info('Операция отменена.');
            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($webmaster) {
                $this->info('Проверка связанных данных...');

                $usersCount = $webmaster->users()->count();
                $conversionsCount = $webmaster->conversions()->count();
                $actionsCount = $webmaster->actions()->count();
                $loanOffersCount = $webmaster->loanOffers()->count();

                if ($usersCount > 0) {
                    $this->warn("Найдено {$usersCount} пользователей, связанных с вебмастером.");
                    $this->info('Обнуляем webmaster_id у связанных пользователей...');
                    $webmaster->users()->update(['webmaster_id' => null]);
                }

                if ($conversionsCount > 0) {
                    $this->warn("Найдено {$conversionsCount} конверсий, связанных с вебмастером.");
                    $action = $this->choice(
                        'Что делать с конверсиями?',
                        ['delete' => 'Удалить', 'nullify' => 'Обнулить webmaster_id'],
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

                $this->info('Удаление вебмастера...');
                $webmaster->delete();

                $this->info("Вебмастер с ID {$webmaster->id} успешно удален.");
            });

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Ошибка при удалении вебмастера: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
