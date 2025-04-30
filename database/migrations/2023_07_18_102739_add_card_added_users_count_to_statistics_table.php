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
        Schema::table('statistics', function (Blueprint $table) {
            $table->unsignedInteger('card_added_users_count')
                ->default(0)
                ->comment('Количество пользователей, привязавших карту')
                ->after('active_users_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropColumn([
                'card_added_users_count',
            ]);
        });
    }
};
