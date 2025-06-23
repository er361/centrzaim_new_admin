<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DumpTablesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump-tables {tables*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump specified database tables';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tables = $this->argument('tables');
        
        if (empty($tables)) {
            $this->error('No tables specified');
            return Command::FAILURE;
        }

        $outputDir = storage_path('app/database-dumps');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $outputFile = "{$outputDir}/dump_{$timestamp}.sql";

        $this->info('Starting database dump...');
        $this->info('Tables: ' . implode(', ', $tables));

        $connection = config('database.default');
        $dbConfig = config("database.connections.{$connection}");

        if (!$dbConfig) {
            $this->error("Database connection '{$connection}' not configured.");
            return Command::FAILURE;
        }

        // Validate tables exist
        $missingTables = [];
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }

        if (!empty($missingTables)) {
            $this->error('The following tables do not exist: ' . implode(', ', $missingTables));
            return Command::FAILURE;
        }

        $host = $dbConfig['host'];
        $port = $dbConfig['port'] ?? 3306;
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];

        $tablesString = implode(' ', $tables);
        
        // Build mysqldump command
        $command = sprintf(
            'mysqldump -h%s -P%s -u%s -p%s %s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            $tablesString,
            escapeshellarg($outputFile)
        );

        $output = [];
        $returnCode = null;
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $fileSize = filesize($outputFile);
            $fileSizeFormatted = number_format($fileSize / 1024 / 1024, 2) . ' MB';
            
            $this->info('');
            $this->info('Dump completed successfully!');
            $this->info("Output file: {$outputFile}");
            $this->info("File size: {$fileSizeFormatted}");
            
            return Command::SUCCESS;
        } else {
            $this->error('Error during dump:');
            foreach ($output as $line) {
                $this->error($line);
            }
            return Command::FAILURE;
        }
    }
}