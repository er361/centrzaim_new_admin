<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Таблица маленькая, можно обновить все записи
        DB::table('detected_users')
            ->update([
                'cost' => 5, // Текущая стоимость 5 рублей
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Нет необходимости, изменяем данные
    }
};
