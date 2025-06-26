<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->string('site_id')->nullable()->after('api_transaction_id');
            $table->string('place_id')->nullable()->after('site_id');
            $table->string('banner_id')->nullable()->after('place_id');
            $table->string('campaign_id')->nullable()->after('banner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->dropColumn(['site_id', 'place_id', 'banner_id', 'campaign_id']);
        });
    }
};
