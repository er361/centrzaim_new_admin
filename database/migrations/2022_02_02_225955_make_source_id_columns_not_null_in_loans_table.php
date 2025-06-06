<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeSourceIdColumnsNotNullInLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedInteger('source_id')
                ->nullable(false)
                ->change();

            $table->unsignedInteger('link_source_id')
                ->nullable(false)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedInteger('source_id')
                ->nullable(true)
                ->change();

            $table->unsignedInteger('link_source_id')
                ->nullable(true)
                ->change();
        });
    }
}
