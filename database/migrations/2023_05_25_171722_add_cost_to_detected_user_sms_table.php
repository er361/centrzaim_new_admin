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
        Schema::table('detected_user_sms', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)
                ->default(0)
                ->after('status')
                ->comment('Стоимость отправки SMS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('detected_user_sms', function (Blueprint $table) {
            $table->dropColumn([
                'cost',
            ]);
        });
    }
};
