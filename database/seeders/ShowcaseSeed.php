<?php

namespace Database\Seeders;

use App\Models\Showcase;
use Illuminate\Database\Seeder;

class ShowcaseSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $items = [
            ['id' => Showcase::ID_PRIVATE, 'name' => 'Витрина аккаунта', 'is_public' => false,],
            ['id' => Showcase::ID_PUBLIC, 'name' => 'Публичная витрина', 'is_public' => true,],
            ['id' => Showcase::ID_RZAEM, 'name' => 'RZaem', 'external_url' => 'https://rzaem.ru', 'is_public' => true,],
            ['id' => Showcase::ID_3AIMI, 'name' => '3aimi', 'external_url' => 'https://3aimi.ru', 'is_public' => true,],
        ];

        $currentShowcases = Showcase::query()->get()->keyBy('id');

        foreach ($items as $item) {
            if ($currentShowcases->has($item['id'])) {
                $currentShowcases->get($item['id'])->update($item);
            } else {
                Showcase::query()->create($item);
            }
        }
    }
}
