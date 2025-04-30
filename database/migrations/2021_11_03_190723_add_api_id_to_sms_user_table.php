<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiIdToSmsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_user', function (Blueprint $table) {
            $table->string('api_id')
                ->nullable()
                ->comment('Внешний идентификатор SMS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_user', function (Blueprint $table) {
            $table->dropColumn(['api_id']);
        });
    }
}
