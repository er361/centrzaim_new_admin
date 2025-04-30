<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateTables extends Command
{
    protected $signature = 'db:truncate';
    protected $description = 'Truncate all tables in the database';

    public function handle()
    {
        Schema::disableForeignKeyConstraints();

        foreach (DB::connection()->getDoctrineSchemaManager()->listTableNames() as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        $this->info('All tables truncated successfully.');
    }
}
