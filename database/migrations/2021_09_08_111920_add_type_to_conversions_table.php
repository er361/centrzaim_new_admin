<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conversions', function(Blueprint $table) {
            $table->string('type')->comment('Тип источника конверсии')->nullable();

            $table->unsignedInteger('sms_id')->comment('ID сообщения')->nullable();
            $table->foreign('sms_id')
                ->references('id')
                ->on('sms')
                ->nullOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conversions', function(Blueprint $table) {
            $table->dropColumn([
                'type',
                'sms_id'
            ]);
        });
    }
}
