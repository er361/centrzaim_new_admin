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
        // Legacy
        if (Schema::hasColumn('users', 'ip_address')) {
            Schema::table('users', function (Blueprint $table) {
                // 45 since: https://stackoverflow.com/a/166157
                $table->string('ip_address', 46)->nullable()->change();
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                // 45 since: https://stackoverflow.com/a/166157
                $table->string('ip_address', 46)->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ip_address',
            ]);
        });
    }
};
