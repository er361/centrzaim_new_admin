<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Было падение, нужно переотправить пользователей
        DB::table('lead_service_user')
            ->where('lead_service_id', 4) // LeadService::ID_DIGITAL_CONTACT
            ->where('created_at', '>=', '2023-08-16 16:30:00')
            ->where('created_at', '<', '2023-08-21 17:15:00')
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
       // Изменяет данные, необратимая миграция
    }
};
