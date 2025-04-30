<?php

namespace App\Jobs;

use App\Actions\CreateWebmasterShowcaseAction;
use App\Models\Webmaster;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateWebmasterShowcaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Webmaster $webmaster)
    {
    }

    public function handle(): void
    {
        $action = new CreateWebmasterShowcaseAction();
        $action->run($this->webmaster);
    }
}
