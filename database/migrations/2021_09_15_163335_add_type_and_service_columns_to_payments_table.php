<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTypeAndServiceColumnsToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger('service')
                ->after('id')
                ->comment('Платежная система')
                ->nullable();

            $table->unsignedInteger('type')
                ->after('amount')
                ->comment('Тип платежа')
                ->nullable();
        });

        // Проставляем старым платежам сервис "Тинькофф"
        DB::statement('UPDATE payments SET service = 1');

        // Проставляем старым платежам типы на основе статусов
        // Тип "Обычный платеж"
        DB::statement('UPDATE payments SET type = 1 WHERE status IN (0, 1, 10, 11)');
        DB::statement('UPDATE payments SET type = 1 WHERE rebill_id IS NOT NULL');

        // Тип "Рекуррентный платеж"
        DB::statement('UPDATE payments SET type = 2 WHERE status IN (1000, 1010, 1011)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'service',
                'type',
            ]);
        });
    }
}
