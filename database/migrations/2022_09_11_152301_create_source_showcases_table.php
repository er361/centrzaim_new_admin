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
        Schema::create('source_showcases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('showcase_id')
                ->comment('Идентификатор витрины');
            $table->foreign('showcase_id')
                ->references('id')
                ->on('showcases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('source_id')
                ->comment('Идентификатор источника');
            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('loan_offer_id')
                ->nullable()
                ->comment('Всплывающий оффер для витрины');

            $table->foreign('loan_offer_id')
                ->references('id')
                ->on('loan_offers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
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
        Schema::drop('source_showcases');
    }
};
