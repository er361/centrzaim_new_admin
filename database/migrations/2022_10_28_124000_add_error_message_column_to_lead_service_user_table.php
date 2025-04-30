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
        Schema::table('lead_service_user', function (Blueprint $table) {
            $table->string('error_message', 1024)
                ->nullable()
                ->comment('Информация об ошибке');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('lead_service_user', function (Blueprint $table) {
            $table->dropColumn([
                'error_message',
            ]);
        });
    }
};
