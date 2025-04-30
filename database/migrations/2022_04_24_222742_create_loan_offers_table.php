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
    public function up()
    {
        Schema::create('loan_offers', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('loan_id');
            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('source_id');
            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('link_source_id');
            $table->foreign('link_source_id')
                ->references('id')
                ->on('sources')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('link')
                ->comment('Ссылка на предложение');
            $table->unsignedBigInteger('priority')
                ->nullable()
                ->comment('Приоритет отображения займа');
            $table->unsignedInteger('type')
                ->comment('Тип предложения')
                ->nullable();
            $table->string('description')
                ->comment('Описание предложения')
                ->nullable();
            $table->boolean('is_hidden')
                ->comment('Скрыто ли предложение на витрине')
                ->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_offers');
    }
};
