<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('webmaster_id')->nullable();
            $table->string('ip');
            $table->string('user_agent', 512)->nullable();
            $table->string('api_transaction_id')->nullable();

            $table->foreign('webmaster_id')
                ->references('id')
                ->on('webmasters');

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
        Schema::dropIfExists('actions');
    }
}
