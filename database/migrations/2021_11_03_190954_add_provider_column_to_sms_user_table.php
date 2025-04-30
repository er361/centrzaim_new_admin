<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderColumnToSmsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_user', function (Blueprint $table) {
            $table->unsignedInteger('service_id')
                ->first()
                ->comment('Провайдер, используемый для отправки SMS.');
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
            $table->dropColumn(['service_id']);
        });
    }
}
