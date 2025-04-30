<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveOldStatusFromPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // "Обычный создан", "Рекуретный создан" -> "Создан"
        DB::statement('UPDATE payments SET status = 0 WHERE status IN (0, 1000)');

        // "Оплачен (стар.)", "Оплачен обычный", "Рекуррентный оплачен" -> "Оплачен"
        DB::statement('UPDATE payments SET status = 10 WHERE status IN (1, 10, 1010)');

        // "Отклонен обычный", "Рекуррентный отклонен" -> "Отклонен"
        DB::statement('UPDATE payments SET status = 11 WHERE status IN (11, 1011)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Миграция изменяет данные, необратима
    }
}
