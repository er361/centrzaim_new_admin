<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('source_showcases', function (Blueprint $table) {
            $table->unsignedInteger('webmaster_id')->nullable()->after('id');

            $table->foreign('webmaster_id')
                ->references('id')
                ->on('webmasters') // Укажите правильное имя таблицы
                ->onDelete('cascade'); // Или другой вариант удаления
        });
    }

    public function down(): void
    {
        Schema::table('source_showcases', function (Blueprint $table) {
            //
            $table->dropColumn('webmaster_id');
        });
    }
};
