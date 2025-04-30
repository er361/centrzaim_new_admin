<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FillCommissionColumnInPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('payments')
            ->where('service', 2) // Impaya
            ->where('status', 11) // Платеж отклонен
            ->where('created_at', '>=', '2022-02-01')
            ->update([
                'commission' => 0.25,
            ]);

        // Взымается комиссия 8%, минимум 25 рублей
        DB::table('payments')
            ->where('service', 2) // Impaya
            ->where('status', 10) // Платеж успешен
            ->update([
                'commission' => DB::raw('GREATEST(IFNULL(amount, 0) * 8 / 100, 25)'),
            ]);
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
