<?php

namespace App\Console\Commands;

use App\Models\SmsProvider;
use App\UpdateSmsFromNameAction;
use Illuminate\Console\Command;

class UpdateSmsFromNameCommand extends Command
{
    protected $signature = 'sms:update-from-name';

    protected $description = 'Обновляет афльфа имена для отправителей SMS';

    public function handle(UpdateSmsFromNameAction $action): void
    {
        $action->run();
    }
}
