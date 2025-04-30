<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $planMapping = [
            2 => 4, // Микрозаймы
            1 => 5, // СрочноЗайм + Ловизаем
            3 => 6, // Малиназайм
            7 => 8, // ЗаемПодРукой
        ];

        foreach ($planMapping as $newPlan => $oldPlan) {
            DB::table('users')
                ->where('payment_plan', $oldPlan)
                ->update([
                    'payment_plan' => $newPlan,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Миграция изменяет данные, необратима
    }
};
