<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $sql = 'DELETE bs1 FROM banner_statistics bs1
            INNER JOIN banner_statistics bs2 
            WHERE 
                bs1.id < bs2.id AND 
                bs1.api_date = bs2.api_date AND
                bs1.banner_id = bs2.banner_id AND
                bs1.source_id = bs2.source_id AND
                bs1.webmaster_id = bs2.webmaster_id;';

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Changes data, irreversible
    }
};
