<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin'),
                'role_id' => Role::ID_ADMIN,
                'remember_token' => '',
                'last_name' => '',
                'logged_at' => Carbon::now(),
                'middlename' => '',
                'credit_sum' => null,
                'credit_days' => null,
                'phone' => '',
                'birthdate' => Carbon::now(),
                'birthplace' => '',
                'citizenship' => '',
                'gender' => null,
                'reg_permanent' => null,
                'reg_region_name' => null,
                'reg_city_name' => '',
                'reg_street' => '',
                'reg_house' => '',
                'reg_flat' => null,
                'fact_country_name' => '',
                'fact_region_name' => '',
                'fact_city_name' => '',
                'fact_street' => '',
                'fact_house' => '',
                'fact_flat' => null,
                'work_experience' => '',
                'passport_title' => '',
                'passport_date' => Carbon::now(),
                'passport_code' => '',
                'ip_address' => '',
            ],
            [
                'name' => 'User',
                'email' => 'sf7kmmr@gmail.com',
                'password' => bcrypt('123'),
                'role_id' => Role::ID_USER,
                'is_active' => 1,
                'is_payment_required' => 0,
                'mphone' => '+77024032110',
                'fill_status' => User::FILL_STATUS_FINISHED
            ],
            [
                'name' => 'Super Admin',
                'email' => 'super@admin.com',
                'password' => bcrypt('superRainboxe361'),
                'role_id' => Role::ID_SUPER_ADMIN,
                'is_active' => 1,
                'is_payment_required' => 0,
                'mphone' => '+77024032110',
                'fill_status' => User::FILL_STATUS_FINISHED
            ]
        ];

        foreach ($items as $item) {
            User::query()->create($item);
        }
    }
}
