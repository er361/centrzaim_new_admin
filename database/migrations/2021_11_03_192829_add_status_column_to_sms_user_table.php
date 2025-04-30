<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToSmsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_user', function (Blueprint $table) {
            $table->unsignedInteger('status')->nullable()->comment('Статус отправки SMS');
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
            $table->dropColumn(['status']);
        });
    }
}
