<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Числа в комментариях ниже (1/-1) взяты из констант в модели User
        // Учитываем только активированных пользователей (иначе они не дошли до шага с заполнением данных)

        // Если не заполнены паспортные данные - он не заполнил ни одного шага (null)
        DB::table('users')
            ->where('is_active', 1)
            ->whereNull('passport_title')
            ->update([
                'fill_status' => null,
            ]);

        // Если не заполнен адрес регистрации, но есть паспортные данные - он заполнил первый шаг (1)
        DB::table('users')
            ->where('is_active', 1)
            ->whereNotNull('passport_title')
            ->whereNull('reg_city_name')
            ->update([
                'fill_status' => 1,
            ]);

        // Если заполнен и адрес регистрации, и паспортные данные - он завершил регистрацию (-1)
        DB::table('users')
            ->where('is_active', 1)
            ->whereNotNull('passport_title')
            ->whereNotNull('reg_city_name')
            ->update([
                'fill_status' => -1,
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
};
