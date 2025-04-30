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
        Schema::create('banner_statistics', function (Blueprint $table) {
            $table->id();

            $table->date('date');

            $table->unsignedBigInteger('banner_id');
            $table->foreign('banner_id')
                ->references('id')
                ->on('banners')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('source_id');
            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedInteger('webmaster_id')
                ->nullable();
            $table->foreign('webmaster_id')
                ->references('id')
                ->on('webmasters')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('impressions');
            $table->unsignedBigInteger('clicks');
            $table->decimal('ctr', 12, 4);
            $table->decimal('revenue', 12, 4);
            $table->decimal('e_cpm', 12, 4);

            $table->timestamps();

            $table->index([
                'date',
                'source_id',
                'webmaster_id',
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
        Schema::dropIfExists('banner_statistics');
    }
};
