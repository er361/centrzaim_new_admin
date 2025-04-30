<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(LeadServiceSeed::class);
        $this->call(RoleSeed::class);
        $this->call(ShowcaseSeed::class);
        $this->call(SourceSeed::class);
        $this->call(UserSeed::class);
        $this->call(UserOfferSeeder::class);
    }
}
