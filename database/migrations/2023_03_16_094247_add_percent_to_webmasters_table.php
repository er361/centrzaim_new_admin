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
        Schema::table('webmasters', function (Blueprint $table) {
            $table->decimal('income_percent')
                ->nullable()
                ->comment('Процент заработка вебмастеру');
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
                'income_percent',
            ]);
        });
    }
};
