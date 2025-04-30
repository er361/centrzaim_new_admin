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
        Schema::table('webmasters', function (Blueprint $table) {
            $table->boolean('is_registration_postback')
                ->default(false)
                ->comment('Отправляем постбэк после регистрации пользователя');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('webmasters', function (Blueprint $table) {
            $table->dropColumn([
                'is_registration_postback',
            ]);
        });
    }
};
