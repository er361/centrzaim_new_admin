<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_providers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Название аккаунта');
            $table->string('api_login')->nullable()->comment('Логин API')->nullable();
            $table->string('api_password')->nullable()->comment('Пароль API')->nullable();
            $table->unsignedInteger('service_id')->comment('Тип аккаунта');
            $table->string('sender')->comment('Отправитель сообщения')->nullable();
            $table->boolean('is_for_activation')->comment('Использовать для активации аккаунтов');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_providers');
    }
}
