<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeApiStatusColumnInConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // У Leads приходит статус approved, мы меняем на константу STATUS_APPROVED из модели Covnersion
        DB::table('conversions')
            ->where('api_status', 'approved')
            ->update(['api_status' => 1]);

        DB::table('conversions')
            ->where('api_status', 'rejected')
            ->update(['api_status' => 2]);

        DB::table('conversions')
            ->where('api_status', 'pending')
            ->update(['api_status' => 3]);
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
