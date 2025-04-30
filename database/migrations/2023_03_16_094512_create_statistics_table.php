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
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->unsignedInteger('source_id')->nullable();
            $table->unsignedInteger('webmaster_id')->nullable();
            $table->integer('webmaster_income_coefficient')->nullable();
            $table->integer('actions_count')->default(0);
            $table->integer('users_count')->default(0);
            $table->integer('active_users_count')->default(0);
            $table->decimal('dashboard_conversions')->default(0);
            $table->decimal('sms_conversions')->default(0);
            $table->decimal('sms_cost_sum')->default(0);
            $table->decimal('payments_sum')->default(0);
            $table->decimal('banners_sum')->default(0);
            $table->integer('postback_count')->default(0);
            $table->decimal('postback_cost_sum')->default(0);
            $table->decimal('total')->default(0);

            $table->timestamps();

            $table->unique([
                'date',
                'webmaster_id',
                'source_id',
            ]);

            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('webmaster_id')
                ->references('id')
                ->on('webmasters')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
