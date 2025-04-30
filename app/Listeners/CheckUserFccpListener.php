<?php

namespace App\Listeners;

use App\Events\UserRegistrationStep1DoneEvent;
use App\Jobs\FetchFccpApiJob;

class CheckUserFccpListener
{
    public function __construct()
    {
    }

    public function handle(UserRegistrationStep1DoneEvent $event): void
    {
        $user = $event->user;
        $fetchFccpApiJob = new FetchFccpApiJob($user);
        dispatch($fetchFccpApiJob)->onQueue('fccp');
    }
}
