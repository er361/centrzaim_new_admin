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
            $table->string('postback_step')
                ->nullable()
                ->comment('Шаг отправки постбэка для вебмастера');
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
                'postback_step',
            ]);
        });
    }
};
