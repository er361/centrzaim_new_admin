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
        Schema::table('sms_providers', function (Blueprint $table) {
            $table->decimal('sms_cost', 10, 2)
                ->default(0)
                ->comment('Стоимость отправки одного SMS сообщения');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('sms_providers', function (Blueprint $table) {
            $table->dropColumn([
                'sms_cost',
            ]);
        });
    }
};
