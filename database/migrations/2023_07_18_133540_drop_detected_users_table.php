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
    public function up(): void
    {
        Schema::drop('detected_users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::create('detected_users', function (Blueprint $table) {
            $table->id();

            $table->string('phone')->unique();
            $table->unsignedInteger('webmaster_id')->nullable();

            $table->timestamp('visited_at')->index();
            $table->decimal('cost', 10, 2)
                ->default(0)
                ->comment('Стоимость определения пользователя');
            $table->string('website')->nullable();
            $table->string('page')->nullable();
            $table->string('email')->nullable();
            $table->string('vkontakte')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('odnoklassniki')->nullable();
            $table->string('referer')->nullable();
            $table->string('ip')->nullable();

            $table->timestamps();

            $table->foreign('webmaster_id')
                ->references('id')
                ->on('webmasters')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};
