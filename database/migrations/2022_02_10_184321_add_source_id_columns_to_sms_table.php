<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceIdColumnsToSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms', function (Blueprint $table) {
            $table->unsignedInteger('link_source_id')
                ->nullable()
                ->comment('Партнерская программа, на которую ведет ссылка');
            $table->foreign('link_source_id')
                ->references('id')
                ->on('sources')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('source_id')
                ->nullable()
                ->comment('Партнерская программа, для пользователей которой показываем ссылку');
            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms', function (Blueprint $table) {
            $table->dropForeign('source_id');
            $table->dropColumn('source_id');

            $table->dropForeign('link_source_id');
            $table->dropColumn('link_source_id');
        });
    }
}
