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
        // При получении списка рекуррентных платежей используются следующие запросы:
        // user_id + status + type + rebill_id
        // user_id + service + error_code
        // user_id + type + status
        // user_id + type + status + created_at
        // user_id + type + status + created_at + error_code

        Schema::table('payments', function (Blueprint $table) {
            $table->index([
                'user_id',
                'type',
                'status',
                'created_at',
            ], 'payments_recurrent_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_recurrent_index');
        });
    }
};
