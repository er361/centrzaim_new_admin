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
        Schema::create('banner_webmaster', function (Blueprint $table) {
            $table->unsignedBigInteger('banner_id');
            $table->foreign('banner_id')
                ->references('id')
                ->on('banners')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('webmaster_id');
            $table->foreign('webmaster_id')
                ->references('id')
                ->on('webmasters')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique([
                'banner_id',
                'webmaster_id',
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
        Schema::dropIfExists('banner_webmaster');
    }
};
