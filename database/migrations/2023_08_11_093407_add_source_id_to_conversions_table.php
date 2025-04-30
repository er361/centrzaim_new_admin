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
        Schema::table('conversions', function (Blueprint $table) {
            $table->unsignedInteger('source_id')
                ->comment('Идентификатор партнерской программы, откуда пришла конверсия')
                ->nullable();

            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
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
        Schema::table('conversions', function (Blueprint $table) {
            $table->dropForeign([
                'source_id',
            ]);

            $table->dropColumn([
                'source_id',
            ]);
        });
    }
};
