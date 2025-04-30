<?php

namespace Database\Seeders;

use App\Models\LeadService;
use Illuminate\Database\Seeder;

class LeadServiceSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['id' => LeadService::ID_Q_ZAEM, 'name' => 'QZaem',],
            ['id' => LeadService::ID_LEADS_TECH, 'name' => 'LeadsTech',],
            ['id' => LeadService::ID_LEADS_MIG_CREDIT, 'name' => 'Leads (МигКредит)',],
            ['id' => LeadService::ID_DIGITAL_CONTACT, 'name' => 'DigitalContact',],
        ];

        $currentLeadServices = LeadService::query()->get()->keyBy('id');

        foreach ($items as $item) {
            if (!$currentLeadServices->has($item['id'])) {
                LeadService::query()->create($item);
            }
        }
    }
}
