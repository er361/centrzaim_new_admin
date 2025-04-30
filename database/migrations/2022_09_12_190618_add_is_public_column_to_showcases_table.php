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
        Schema::table('showcases', function (Blueprint $table) {
            $table->boolean('is_public')
                ->comment('Является ли витрина публично доступной');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('showcases', function (Blueprint $table) {
            $table->dropColumn([
                'is_public',
            ]);
        });
    }
};
