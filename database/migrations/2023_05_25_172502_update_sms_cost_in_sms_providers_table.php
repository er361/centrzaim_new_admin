<?php

use Illuminate\Database\Migrations\Migration;
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
        DB::table('sms_providers')
            ->update([
                'sms_cost' => 4.2, // Стоимость отправки одного SMS сообщения на момент написания миграции
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Изменяет данные, необратима
    }
};
