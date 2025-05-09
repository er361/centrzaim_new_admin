<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('postbacks', function (Blueprint $table) {
            $table->string('remote_user_id')
                ->nullable()
                ->comment('Отправленный в ПП идентификатор пользователя');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('postbacks', function (Blueprint $table) {
            $table->dropColumn([
                'remote_user_id',
            ]);
        });
    }
};
