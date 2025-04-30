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
        Schema::create('sms_excluded_webmaster', function (Blueprint $table) {
            $table->unsignedInteger('webmaster_id');
            $table->foreign('webmaster_id')
                ->references('id')
                ->on('webmasters')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('sms_id');
            $table->foreign('sms_id')
                ->references('id')
                ->on('sms')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique([
                'webmaster_id',
                'sms_id',
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
        Schema::dropIfExists('sms_excluded_webmaster');
    }
};
