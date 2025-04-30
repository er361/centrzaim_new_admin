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
        Schema::table('loan_offers', function (Blueprint $table) {
            $table->renameColumn('type', 'showcase_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('loan_offers', function (Blueprint $table) {
            $table->renameColumn('showcase_id', 'type');
        });
    }
};
