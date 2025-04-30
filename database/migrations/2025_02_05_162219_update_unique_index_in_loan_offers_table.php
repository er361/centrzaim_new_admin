<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('loan_offers', function (Blueprint $table) {
            // Добавляем новый уникальный индекс с webmaster_id
//            $table->unique(['loan_id', 'source_id', 'showcase_id', 'webmaster_id', 'deleted_at'], 'loan_offers_unique_with_webmaster');
//            $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
//            $table->foreign('showcase_id')->references('id')->on('showcases')->onDelete('cascade');
//            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('loan_offers', function (Blueprint $table) {
            // Удаляем новый индекс
            $table->dropUnique('loan_offers_unique_with_webmaster');
        });
    }
};
