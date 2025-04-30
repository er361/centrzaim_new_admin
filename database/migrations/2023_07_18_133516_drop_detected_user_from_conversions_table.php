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
        Schema::table('conversions', function (Blueprint $table) {
            $table->dropForeign([
                'detected_user_id',
            ]);

            $table->dropColumn([
                'detected_user_id',
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
        Schema::table('conversions', function (Blueprint $table) {
            $table->unsignedBigInteger('detected_user_id')
                ->nullable()
                ->after('user_id');

            $table->foreign('detected_user_id')
                ->references('id')
                ->on('detected_users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};
