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
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger('payment_number')
                ->nullable()
                ->comment('Порядковый номер платежа в рамках итерации');
            $table->unsignedInteger('iteration_number')
                ->nullable()
                ->comment('Порядковый номер итерации');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_number',
                'iteration_number',
            ]);
        });
    }
};
