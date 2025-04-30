<?php

use App\Models\Webmaster;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('banner_statistics', function (Blueprint $table) {
            // Сначала удаляем внешний ключ
            $table->dropForeign(['webmaster_id']);
            $table->dropForeign(['source_id']);

            // Затем модифицируем колонку, делая её nullable
            $table->unsignedInteger('webmaster_id')->nullable()->change();
            $table->unsignedInteger('source_id')->nullable()->change();
        });
    }
};
