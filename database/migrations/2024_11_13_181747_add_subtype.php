<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('subtype')
                ->nullable()
                ->comment('Подтип платежа, 1 - месячный платеж, 2 - еженедельный платеж, 3 - повтор после неудачного месячного платежа')
                ->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
            $table->dropColumn('subtype');
        });
    }
};
