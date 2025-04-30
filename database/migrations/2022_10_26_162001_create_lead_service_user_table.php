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
    public function up()
    {
        Schema::create('lead_service_user', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lead_service_id');
            $table->foreign('lead_service_id')
                ->references('id')
                ->on('lead_services')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique([
                'user_id',
                'lead_service_id',
            ]);

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
        Schema::dropIfExists('lead_service_user');
    }
};
