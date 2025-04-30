<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FillSmsProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('sms_providers')->insert([
            'name' => 'Основной MySMPP',
            'api_login' => null,
            'api_password' => null,
            'service_id' => 1, // SmsProvider::SERVICE_MY_SMPP
            'sender' => null,
            'is_for_activation' => true,
        ]);

        DB::table('sms_providers')->insert([
            'name' => 'Основной SMS.ru',
            'api_login' => null,
            'api_password' => null,
            'service_id' => 2, // SmsProvider::SERVICE_SMS_RU
            'sender' => null,
            'is_for_activation' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Миграция изменяет данные, необратимая
    }
}
