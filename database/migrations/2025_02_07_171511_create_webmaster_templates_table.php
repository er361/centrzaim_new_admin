<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('webmaster_templates');
        Schema::create('webmaster_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('source_id');
            $table->unsignedBigInteger('showcase_id');
            $table->unsignedInteger('webmaster_id')->nullable();

            $table->foreign('source_id')->references('id')->on('sources');
            $table->foreign('showcase_id')->references('id')->on('showcases');
            $table->foreign('webmaster_id')->references('id')->on('webmasters');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webmaster_templates');
    }
};
