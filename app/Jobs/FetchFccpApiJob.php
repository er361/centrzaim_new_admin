<?php

namespace App\Jobs;

use App\Models\Fccp;
use App\Models\User;
use App\Services\FccpApiService\FccpApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchFccpApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(private User $user)
    {

    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $fccpApi = new FccpApi();

        $data = $fccpApi->searchFiz(
            $this->user->name,
            $this->user->last_name,
            $this->user->middlename,
            $this->user->birthdate
        );

        $fccp = new Fccp();
        $fccp->info = $data;
        $fccp->user_id = $this->user->id;
        $fccp->updateOrCreate(['user_id' => $this->user->id], ['info' => $data]);
    }
}
