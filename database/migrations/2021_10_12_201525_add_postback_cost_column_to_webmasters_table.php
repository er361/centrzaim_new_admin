<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostbackCostColumnToWebmastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webmasters', function (Blueprint $table) {
            $table->decimal('postback_cost', 10)
                ->nullable()
                ->comment('Стоимость конверсии');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webmasters', function (Blueprint $table) {
            $table->dropColumn('postback_cost');
        });
    }
}
