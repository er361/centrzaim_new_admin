<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebmastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webmasters', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('source_id')->comment('Партнерская программа');
            $table->string('api_id')->comment('Внешний идентификатор вебмастера');

            $table->unique(['api_id', 'source_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webmasters');
    }
}
