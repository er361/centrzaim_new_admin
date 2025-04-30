<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('banner_statistics', function (Blueprint $table) {
            $table->unique([
                'api_date',
                'source_id',
                'webmaster_id',
                'banner_id',
            ], 'banner_statistics_unique');

            $table->dropIndex('banner_statistics_date_source_id_webmaster_id_index');
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
            $table->dropUnique('banner_statistics_unique');

            $table->index([
               'api_date',
               'source_id',
               'webmaster_id',
            ], 'banner_statistics_date_source_id_webmaster_id_index');
        });
    }
};
