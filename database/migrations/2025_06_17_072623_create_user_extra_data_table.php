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
        Schema::create('user_extra_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('source')->index();
            $table->string('site_id')->nullable();
            $table->string('place_id')->nullable();
            $table->string('banner_id')->nullable();
            $table->string('campaign_id')->nullable();
            $table->string('click_id')->nullable();
            $table->string('webmaster_id')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_extra_data');
    }
};
