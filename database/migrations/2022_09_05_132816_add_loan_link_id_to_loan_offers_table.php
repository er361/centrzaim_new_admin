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
        Schema::table('loan_offers', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_link_id')->nullable();
            $table->foreign('loan_link_id')
                ->references('id')
                ->on('loan_links')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
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
            $table->dropColumn([
                'loan_link_id',
            ]);
        });
    }
};
