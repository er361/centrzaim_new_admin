<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1531510288UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name');
            }
            if (!Schema::hasColumn('users', 'logged_at')) {
                $table->datetime('logged_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'premium_till')) {
                $table->datetime('premium_till')->nullable();
            }
            if (!Schema::hasColumn('users', 'middlename')) {
                $table->string('middlename');
            }
            if (!Schema::hasColumn('users', 'credit_sum')) {
                $table->integer('credit_sum')->nullable();
            }
            if (!Schema::hasColumn('users', 'credit_days')) {
                $table->integer('credit_days')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone');
            }
            if (!Schema::hasColumn('users', 'overdue_loans')) {
                $table->string('overdue_loans');
            }
            if (!Schema::hasColumn('users', 'birthdate')) {
                $table->date('birthdate')->nullable();
            }
            if (!Schema::hasColumn('users', 'birthplace')) {
                $table->string('birthplace');
            }
            if (!Schema::hasColumn('users', 'citizenship')) {
                $table->string('citizenship');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->integer('gender')->nullable();
            }
            if (!Schema::hasColumn('users', 'reg_permanent')) {
                $table->integer('reg_permanent')->nullable();
            }
            if (!Schema::hasColumn('users', 'reg_region_name')) {
                $table->string('reg_region_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'reg_city_name')) {
                $table->string('reg_city_name');
            }
            if (!Schema::hasColumn('users', 'reg_street')) {
                $table->string('reg_street');
            }
            if (!Schema::hasColumn('users', 'reg_house')) {
                $table->string('reg_house');
            }
            if (!Schema::hasColumn('users', 'reg_flat')) {
                $table->string('reg_flat')->nullable();
            }
            if (!Schema::hasColumn('users', 'fact_country_name')) {
                $table->string('fact_country_name');
            }
            if (!Schema::hasColumn('users', 'fact_region_name')) {
                $table->string('fact_region_name');
            }
            if (!Schema::hasColumn('users', 'fact_city_name')) {
                $table->string('fact_city_name');
            }
            if (!Schema::hasColumn('users', 'fact_street')) {
                $table->string('fact_street');
            }
            if (!Schema::hasColumn('users', 'fact_house')) {
                $table->string('fact_house');
            }
            if (!Schema::hasColumn('users', 'fact_flat')) {
                $table->string('fact_flat')->nullable();
            }
            if (!Schema::hasColumn('users', 'work_experience')) {
                $table->string('work_experience');
            }
            if (!Schema::hasColumn('users', 'passport_title')) {
                $table->string('passport_title');
            }
            if (!Schema::hasColumn('users', 'passport_date')) {
                $table->date('passport_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'passport_code')) {
                $table->string('passport_code');
            }

            if (!Schema::hasColumn('users', 'timer_shown')) {
                $table->boolean('timer_shown')->default(0);
            }

            if (!Schema::hasColumn('users', 'comment')) {
                $table->text('comment')->nullable();
            }

            if (!Schema::hasColumn('users', 'first_name')) {
                $table->text('first_name')->nullable();
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(false);
            }

            if (!Schema::hasColumn('users', 'activation_code')) {
                $table->text('activation_code')->nullable();
            }

            if (!Schema::hasColumn('users', 'utm_source')) {
                $table->string('utm_source')->nullable();
            }

            if (!Schema::hasColumn('users', 'utm_campaign')) {
                $table->string('utm_campaign')->nullable();
            }

            if (!Schema::hasColumn('users', 'utm_content')) {
                $table->string('utm_content')->nullable();
            }
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
            $table->dropColumn('last_name');
            $table->dropColumn('logged_at');
            $table->dropColumn('premium_till');
            $table->dropColumn('middlename');
            $table->dropColumn('credit_sum');
            $table->dropColumn('credit_days');
            $table->dropColumn('phone');
            $table->dropColumn('overdue_loans');
            $table->dropColumn('birthdate');
            $table->dropColumn('birthplace');
            $table->dropColumn('citizenship');
            $table->dropColumn('gender');
            $table->dropColumn('reg_permanent');
            $table->dropColumn('reg_region_name');
            $table->dropColumn('reg_city_name');
            $table->dropColumn('reg_street');
            $table->dropColumn('reg_house');
            $table->dropColumn('reg_flat');
            $table->dropColumn('fact_country_name');
            $table->dropColumn('fact_region_name');
            $table->dropColumn('fact_city_name');
            $table->dropColumn('fact_street');
            $table->dropColumn('fact_house');
            $table->dropColumn('fact_flat');
            $table->dropColumn('work_experience');
            $table->dropColumn('passport_title');
            $table->dropColumn('passport_date');
            $table->dropColumn('passport_code');

            $table->dropColumn('timer_shown');
            $table->dropColumn('comment');
            $table->dropColumn('first_name');
            $table->dropColumn('activation_code');
            $table->dropColumn('is_active');

            $table->dropColumn('utm_source');
            $table->dropColumn('utm_campaign');
            $table->dropColumn('utm_content');
        });

    }
}
