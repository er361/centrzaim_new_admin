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
        Schema::table('banner_statistics', function (Blueprint $table) {
            $table->renameColumn('date', 'api_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('banner_statistics', function (Blueprint $table) {
            $table->renameColumn('api_date', 'date');
        });
    }
};
