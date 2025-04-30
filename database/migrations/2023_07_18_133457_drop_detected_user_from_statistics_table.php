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
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropColumn([
                'sms_detected_user_conversions',
                'sms_detected_user_cost_sum',
            ]);
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
            $table->decimal('sms_detected_user_conversions')
                ->default(0)
                ->after('sms_cost_sum');

            $table->decimal('sms_detected_user_cost_sum')
                ->default(0)
                ->after('sms_detected_user_conversions');
        });
    }
};
