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
        Schema::table('webmasters', function (Blueprint $table) {
            $table->boolean('is_payment_required')
                ->default(true)
                ->comment('Показывать ли форму оплаты пользователям');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('webmasters', function (Blueprint $table) {
            $table->dropColumn([
                'is_payment_required',
            ]);
        });
    }
};
