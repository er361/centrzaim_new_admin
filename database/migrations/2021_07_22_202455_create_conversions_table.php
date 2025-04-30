<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->string('api_conversion_id')->comment('ID конверсии');
            $table->string('api_transaction_id')->comment('ID транзакции');
            $table->string('api_adv_sub_id')->nullable()->comment('SubID рекламодателя');
            $table->dateTime('api_created_at')->nullable()->comment('Дата создания конверсии');
            $table->string('api_status')->nullable()->comment('Статус конверсии (approved, rejected, pending)');
            $table->decimal('api_payout', 10, 2)->nullable()->comment('Заработок');
            $table->string('api_payout_type')->nullable()->comment('Тип расчета выплаты');
            $table->string('api_user_agent', 512)->nullable()->comment('User Agent');
            $table->string('api_offer_id')->nullable()->comment('ID оффера');
            $table->string('api_affiliate_id')->nullable()->comment('ID вебмастера');
            $table->string('api_source')->nullable()->comment('Партнерский источник');
            $table->string('api_ip')->nullable()->comment('IP адрес');
            $table->boolean('api_is_test')->nullable()->comment('Тестовая конверсия');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversions');
    }
}
