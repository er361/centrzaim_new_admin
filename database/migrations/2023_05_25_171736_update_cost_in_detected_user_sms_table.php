<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::table('detected_user_sms')
            ->orderBy('detected_user_id')
            ->chunkById(1000, function (Collection $detectedUserSms) {
                DB::table('detected_user_sms')
                    ->whereIn('detected_user_id', $detectedUserSms->pluck('detected_user_id')->toArray())
                    ->update([
                        'cost' => 4.2, // На момент запуска актуальная стоимость (но у старых SMS была другая стоимость отправки)
                    ]);
            }, 'detected_user_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
       // Нет необходимости откатывать миграцию
    }
};
