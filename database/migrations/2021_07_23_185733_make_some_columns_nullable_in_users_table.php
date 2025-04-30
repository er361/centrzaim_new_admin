<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeSomeColumnsNullableInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('overdue_loans')->nullable(true)->change();
            $table->string('birthplace')->nullable(true)->change();
            $table->string('citizenship')->nullable(true)->change();
            $table->string('reg_city_name')->nullable(true)->change();
            $table->string('reg_street')->nullable(true)->change();
            $table->string('reg_house')->nullable(true)->change();
            $table->string('fact_country_name')->nullable(true)->change();
            $table->string('fact_region_name')->nullable(true)->change();
            $table->string('fact_city_name')->nullable(true)->change();
            $table->string('fact_street')->nullable(true)->change();
            $table->string('fact_house')->nullable(true)->change();
            $table->string('work_experience')->nullable(true)->change();
            $table->string('passport_title')->nullable(true)->change();
            $table->string('passport_code')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('overdue_loans')->nullable(false)->change();
            $table->string('birthplace')->nullable(false)->change();
            $table->string('citizenship')->nullable(false)->change();
            $table->string('reg_city_name')->nullable(false)->change();
            $table->string('reg_street')->nullable(false)->change();
            $table->string('reg_house')->nullable(false)->change();
            $table->string('fact_country_name')->nullable(false)->change();
            $table->string('fact_region_name')->nullable(false)->change();
            $table->string('fact_city_name')->nullable(false)->change();
            $table->string('fact_street')->nullable(false)->change();
            $table->string('fact_house')->nullable(false)->change();
            $table->string('work_experience')->nullable(false)->change();
            $table->string('passport_title')->nullable(false)->change();
            $table->string('passport_code')->nullable(false)->change();
        });
    }
}
