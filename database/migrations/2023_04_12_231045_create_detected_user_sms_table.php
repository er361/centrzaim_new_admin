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
    public function up(): void
    {
        Schema::create('detected_user_sms', function (Blueprint $table) {
            $table->unsignedBigInteger('detected_user_id');
            $table->unsignedInteger('sms_id');

            $table->string('api_id')
                ->nullable()
                ->comment('Внешний идентификатор SMS');

            $table->string('error', 512)
                ->nullable()
                ->comment('Текст ошибки при отправке SMS');

            $table->unsignedInteger('service_id')
                ->comment('Провайдер, используемый для отправки SMS.');

            $table->unsignedInteger('status')
                ->nullable()
                ->comment('Статус отправки SMS');

            $table->timestamps();

            $table->foreign('detected_user_id')
                ->references('id')
                ->on('detected_users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('sms_id')
                ->references('id')
                ->on('sms')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique([
                'detected_user_id',
                'sms_id',
            ]);

            $table->index([
                'created_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('detected_user_sms');
    }
};
