<?php

namespace App\Console\Commands;

use App\Models\LeadService;
use App\Models\User;
use App\Services\LeadService\Exceptions\UserDuplicateException;
use App\Services\LeadService\Exceptions\UserNotEligibleException;
use App\Services\LeadService\LeadServiceFactory;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendUsersToLeadServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:send-to-lead-services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправяляет анкеты пользователей, которые зарегистрированы на сайте, в сервисы по обработке лидов.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Начинаем отправку анкет пользователей во внешние сервисы.');

        /** @var LeadServiceFactory $leadServiceFactory */
        $leadServiceFactory = App::make(LeadServiceFactory::class);
        $now = CarbonImmutable::now();

        LeadService::query()
            ->whereNotNull('registered_after')
            ->eachById(function (LeadService $leadServiceModel) use ($now, $leadServiceFactory) {
                $this->info("Начинаем отправку пользователей по LeadService #{$leadServiceModel->id}");

                $createdAtBefore = $now->subMinutes($leadServiceModel->delay_minutes);
                $leadService = $leadServiceFactory->getInstance($leadServiceModel);

                User::query()
                    ->whereDoesntHave('leadServices', function (Builder $query) use ($leadServiceModel) {
                        $query->where('lead_services.id', $leadServiceModel->id);
                    })
                    ->whereCreatedAtAfter($leadServiceModel->registered_after)
                    ->whereCreatedAtBefore($createdAtBefore)
                    ->whereHasRealEmail()
                    ->with([
                        'webmaster',
                    ])
                    ->eachById(function (User $user) use ($leadService, $leadServiceModel) {
                        $errorMessage = null;

                        try {
                            $leadService->send($user);
                            $this->info("Отправили пользователя #{$user->id}");
                        } catch (UserDuplicateException|UserNotEligibleException $e) {
                            // Игнорируем ошибку, помечаем как отправленного
                            $errorMessage = $e->getMessage();
                            $this->warn("Ошибка при отправке пользователя #{$user->id}: {$e->getMessage()}");
                        } catch (Throwable $e) {
                            $this->error("Ошибка при отправке пользователя #{$user->id}: {$e->getMessage()}");
                            report($e);
                            return;
                        }

                        $user->leadServices()->attach($leadServiceModel->id, [
                            'error_message' => $errorMessage,
                        ]);
                    });
            });

        Log::info('Завершили отправку анкет пользователей во внешние сервисы.');

        return 0;
    }
}
