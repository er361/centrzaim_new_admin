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
    public function up()
    {
        Schema::table('webmasters', function (Blueprint $table) {
            $table->text('comment')
                ->comment('Комментарий')
                ->after('api_id')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webmasters', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};
