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
            $table->dropForeign([
                'showcase_id',
            ]);

            $table->dropForeign([
                'loan_id',
            ]);

            $table->dropForeign([
                'source_id',
            ]);

            $table->dropUnique([
                'loan_id',
                'source_id',
                'showcase_id',
            ]);

            $table->unique([
                'loan_id',
                'source_id',
                'showcase_id',
                'deleted_at',
            ]);

            $table->foreign('showcase_id')
                ->references('id')
                ->on('showcases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
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
            $table->dropForeign([
                'showcase_id',
            ]);

            $table->dropForeign([
                'loan_id',
            ]);

            $table->dropForeign([
                'source_id',
            ]);

            $table->dropUnique([
                'loan_id',
                'source_id',
                'showcase_id',
                'deleted_at',
            ]);

            $table->unique([
                'loan_id',
                'source_id',
                'showcase_id',
            ]);

            $table->foreign('showcase_id')
                ->references('id')
                ->on('showcases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};
