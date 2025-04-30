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
        Schema::table('sms', function (Blueprint $table) {
            $table->unsignedInteger('related_sms_id')
                ->comment('ID связанного сообщения')
                ->nullable();

            $table->foreign('related_sms_id')
                ->references('id')
                ->on('sms')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('sms', function (Blueprint $table) {
            $table->dropColumn([
                'related_sms_id',
            ]);
        });
    }
};
