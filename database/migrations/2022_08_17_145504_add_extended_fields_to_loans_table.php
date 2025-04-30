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
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedDecimal('rating')
                ->nullable()
                ->comment('Рейтинг предложения');
            $table->string('amount')
                ->nullable()
                ->comment('Сумма займа');
            $table->string('issuing_time')
                ->nullable()
                ->comment('Время выдачи займа');
            $table->string('issuing_period')
                ->nullable()
                ->comment('Срок выдачи займа');
            $table->string('issuing_bid')
                ->nullable()
                ->comment('Ставка выдачи займа');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'rating',
                'amount',
                'issuing_time',
                'issuing_period',
                'issuing_bid',
            ]);
        });
    }
};
