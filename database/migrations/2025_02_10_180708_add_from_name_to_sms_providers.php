<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sms_providers', function (Blueprint $table) {
            $table->json('from_name')->nullable()->comment('Имена отправителей для SMS');
        });
    }

    public function down(): void
    {
        Schema::table('sms_providers', function (Blueprint $table) {
            //
            $table->dropColumn('from_name');
        });
    }
};
