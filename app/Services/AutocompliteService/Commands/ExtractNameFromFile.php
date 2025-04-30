<?php

namespace App\Services\AutocompliteService\Commands;

use App\Services\AutocompliteService\Models\FullNameHelper;
use Illuminate\Console\Command;

class ExtractNameFromFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extract:text {inputFile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts text field from JSONL file and saves to database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Получаем имя входного файла из аргументов команды
        $inputFile = $this->argument('inputFile');

        // Открываем исходный JSONL файл для чтения
        if (!file_exists($inputFile)) {
            $this->error("Input file not found: $inputFile");
            return parent::FAILURE;
        }

        $inputHandle = fopen($inputFile, 'r');
        if (!$inputHandle) {
            $this->error("Unable to open input file: $inputFile");
            return parent::FAILURE;
        }

        // Получаем общее количество строк в файле для прогресс-бара
        $totalLines = 0;
        while (fgets($inputHandle) !== false) {
            $totalLines++;
        }
        rewind($inputHandle);

        // Инициализация прогресс-бара
        $bar = $this->output->createProgressBar($totalLines);
        $bar->start();

        // Читаем и обрабатываем строки файла
        while (($line = fgets($inputHandle)) !== false) {
            $data = json_decode($line, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['text'])) {
                // Сохраняем значение в базу данных
//                dump("Text extracted: {$data['text']}");
                if(!$data['text'])
                    continue;

                FullNameHelper::query()->create([
                    'value' => $data['text'],
                    'type' => FullNameHelper::TYPE_FATHER_NAME,
                    'gender' => array_key_exists('gender', $data) ? $data['gender'] : ''
                ]);

            }
            // Обновление прогресс-бара
            $bar->advance();
        }

        // Закрываем файл
        fclose($inputHandle);

        // Завершаем прогресс-бар
        $bar->finish();
        $this->info("\nTexts extracted and saved to database successfully");
        return parent::SUCCESS;
    }
}
