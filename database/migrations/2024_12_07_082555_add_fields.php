<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->string('license')->nullable();
            $table->integer('api_id');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
            $table->dropColumn('license');
            $table->dropColumn('api_id');
        });
    }
};
