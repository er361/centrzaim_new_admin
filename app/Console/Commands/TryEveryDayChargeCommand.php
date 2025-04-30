<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\CreatePaymentService\TryEveryDayHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class TryEveryDayChargeCommand extends Command
{
    protected $signature = 'every:try-day-charge';

    protected $description = 'Списывает деньги с пользователей, у которых нет платежа за неделю.';

    public function handle(): void
    {
        $usersWithWeekPayments = App::make(UserRepository::class)->getUsersWithErrors(0);
        $this->info(sprintf('count of users to every day charge : %d',$usersWithWeekPayments->count()));
        $usersWithWeekPayments
            ->with(['latestRecurrentPayment'])
            ->orderBy('id')
            ->eachById(function (User $user) {

                $tryEveryDayHandler = new TryEveryDayHandler();
                $this->info(sprintf('Планируем списание платежа по пользователю.(ежедневная попытка), user_id: %d, subtype: %s', $user->id, Payment::SUBTYPE_WEEKLY));
                Log::channel('payments')->debug('Планируем списание платежа по пользователю.(ежедневная попытка)', [
                    'user_id' => $user->id,
                    'subtype' => Payment::SUBTYPE_WEEKLY
                ]);
                $tryEveryDayHandler->try($user);
            }, 100);


    }
}
