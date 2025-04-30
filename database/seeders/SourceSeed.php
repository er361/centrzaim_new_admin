<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // id взяты из app/Models/Source.php
        $items = [
            ['id' => 1, 'name' => 'Leads',],
            ['id' => 2, 'name' => 'Guru Leads',],
            ['id' => 3, 'name' => 'Прямые вебмастера',],
            ['id' => 4, 'name' => 'ЛидГид',],
            ['id' => 5, 'name' => 'LeadCraft',],
            ['id' => 6, 'name' => 'LeadBit',],
            ['id' => 7, 'name' => 'Click2Money',],
            ['id' => 8, 'name' => 'LeadsTech',],
            ['id' => 9, 'name' => 'Affise',],
            ['id' => 10, 'name' => 'Fin CPA Network',],
            ['id' => 11, 'name' => 'XPartners',],
            ['id' => 12, 'name' => 'LeadTarget',],
            ['id' => 13, 'name' => 'Финкорт',],
            ['id' => 14, 'name' => 'ЛинкМани',],
            ['id' => 15, 'name' => 'Альянс',],
            ['id' => 16, 'name' => 'Bankiros',],
            ['id' => 17, 'name' => 'Sravni',],
        ];

        $currentSource = Source::query()->get()->keyBy('id');

        foreach ($items as $item) {
            if (!$currentSource->has($item['id'])) {
                Source::query()->create($item);
            }
        }
    }
}
