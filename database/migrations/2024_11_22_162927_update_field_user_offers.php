<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_offers', function (Blueprint $table) {
            $table->renameColumn('offers', 'repeated_offers');
        });
    }

    public function down(): void
    {
        Schema::table('user_offers', function (Blueprint $table) {
            //
            $table->renameColumn('repeated_offers', 'offers');
        });
    }
};
