<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pushes')) {
            Schema::create('pushes', function (Blueprint $table) {

                $table->increments('id');

                $table->string('name');
                $table->text('text');
                $table->integer('delay');
                $table->boolean('enabled')->default(false);
				$table->string('url');
				$table->integer('visits')->default(0);

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pushes');
    }
}
