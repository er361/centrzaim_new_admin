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
        Schema::table('sms_user', function (Blueprint $table) {
            $table->string('error', 512)
                ->nullable()
                ->comment('Текст ошибки при отправке SMS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('sms_user', function (Blueprint $table) {
            $table->dropColumn([
                'error',
            ]);
        });
    }
};
