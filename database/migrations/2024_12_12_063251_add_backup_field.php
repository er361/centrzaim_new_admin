<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loan_offers', function (Blueprint $table) {
            $table->addColumn('boolean', 'is_backup', ['default' => false]);
        });
    }

    public function down(): void
    {
        Schema::table('loan_offers', function (Blueprint $table) {
            //
            $table->dropColumn('is_backup');
        });
    }
};
