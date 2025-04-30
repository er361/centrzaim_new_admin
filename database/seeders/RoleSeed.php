<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['id' => 1, 'title' => 'Администратор',],
            ['id' => 2, 'title' => 'Пользователь',],
            ['id' => 3, 'title' => 'Сотрудник КЦ',],
            ['id' => 4, 'title' => 'Работа с трафиком',],
            ['id' => 100, 'title' => 'Super Admin',],
        ];

        $currentRoles = Role::query()->get()->keyBy('id');

        foreach ($items as $item) {
            if (!$currentRoles->has($item['id'])) {
                Role::query()->create($item);
            }
        }
    }
}
