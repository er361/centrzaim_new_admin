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
            $table->dropForeign([
                'link_source_id',
            ]);
        });

        Schema::table('loan_offers', function (Blueprint $table) {
            $table->dropColumn([
                'link',
                'description',
                'link_source_id',
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
            $table->string('link')
                ->comment('Ссылка на предложение');

            $table->string('description')
                ->comment('Описание предложения')
                ->nullable();

            $table->unsignedInteger('link_source_id')
                ->nullable()
                ->comment('Партнерская программа, на которую ведет ссылка');

            $table->foreign('link_source_id')
                ->references('id')
                ->on('sources')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};
