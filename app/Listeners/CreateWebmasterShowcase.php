<?php

namespace App\Listeners;

use App\Events\WebmasterRegistered;
use App\Jobs\CreateWebmasterShowcaseJob;

class CreateWebmasterShowcase
{
    public function __construct()
    {
    }

    public function handle(WebmasterRegistered $event): void
    {
        dispatch(new CreateWebmasterShowcaseJob($event->webmaster))
            ->onQueue('webmaster');
    }
}
