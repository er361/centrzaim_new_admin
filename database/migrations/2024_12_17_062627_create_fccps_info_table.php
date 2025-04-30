<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fccps_info', function (Blueprint $table) {
            $table->id();
            $table->json('info');
            $table->timestamps();
            $table->unsignedInteger('user_id'); // Приводим тип к unsignedInteger
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fccps_info');
    }
};
