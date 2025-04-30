<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sms', function (Blueprint $table) {
            $table->string('from')->nullable()->after('text');
        });
    }

    public function down(): void
    {
        Schema::table('sms', function (Blueprint $table) {
            //
            $table->dropColumn('from');
        });
    }
};
