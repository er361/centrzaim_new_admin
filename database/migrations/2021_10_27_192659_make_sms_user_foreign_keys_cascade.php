<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeSmsUserForeignKeysCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `sms_user` DROP FOREIGN KEY `sms_user_sms_id_foreign`;');
        DB::statement('ALTER TABLE `sms_user` ADD CONSTRAINT `sms_user_sms_id_foreign` FOREIGN KEY (`sms_id`) REFERENCES `sms`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;');
        DB::statement('ALTER TABLE `sms_user` DROP FOREIGN KEY `sms_user_user_id_foreign`;');
        DB::statement('ALTER TABLE `sms_user` ADD CONSTRAINT `sms_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `sms_user` DROP FOREIGN KEY `sms_user_sms_id_foreign`;');
        DB::statement('ALTER TABLE `sms_user` ADD CONSTRAINT `sms_user_sms_id_foreign` FOREIGN KEY (`sms_id`) REFERENCES `sms`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
        DB::statement('ALTER TABLE `sms_user` DROP FOREIGN KEY `sms_user_user_id_foreign`;');
        DB::statement('ALTER TABLE `sms_user` ADD CONSTRAINT `sms_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
    }
}
