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
            $table->unsignedBigInteger('showcase_id')
                ->change();
        });

        Schema::table('loan_offers', function (Blueprint $table) {
            $table->foreign('showcase_id')
                ->references('id')
                ->on('showcases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique([
                'loan_id',
                'source_id',
                'showcase_id',
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
        Schema::table('loan_offers', function (Blueprint $table) {
            $table->dropForeign([
                'showcase_id',
            ]);

            $table->dropUnique([
                'loan_id',
                'source_id',
                'showcase_id',
            ]);
        });
    }
};
