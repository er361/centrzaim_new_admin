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
        DB::table('sms_user')
            ->orderBy('user_id')
            ->chunkById(1000, function (Collection $smsUsers) {
                DB::table('sms_user')
                    ->whereIn('user_id', $smsUsers->pluck('user_id')->toArray())
                    ->update([
                        'cost' => 4.2, // На момент запуска актуальная стоимость (но у старых SMS была другая стоимость отправки)
                    ]);
            }, 'user_id');
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
