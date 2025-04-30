<?php

use App\Models\Conversion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FillTypeColumnInConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Conversion::TYPE_DASHBOARD === 1
        DB::statement('update conversions set type=1 where type is null');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Миграция изменяет данные, необратима
    }
}
