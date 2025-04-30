<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FillSourceIdColumnsInLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Изначально все предложения заполнены для Leads
        DB::table('loans')->update(['source_id' => 1]);
        DB::table('loans')->update(['link_source_id' => 1]);
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
