<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('lead_services', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                ->unique()
                ->comment('Название сервиса');
            $table->dateTime('registered_after')
                ->nullable()
                ->comment('Отправлять анкеты, зарегистрированные после');
            $table->unsignedInteger('delay_minutes')
                ->nullable()
                ->comment('Задержка в минутах перед отправкой анкеты');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('lead_services');
    }
};
