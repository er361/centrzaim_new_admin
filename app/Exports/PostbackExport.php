<?php

namespace App\Exports;

use App\Builders\PostbackBuilder;
use App\Models\Postback;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PostbackExport extends TextValueBinder implements FromQuery, WithMapping, WithHeadings, WithCustomChunkSize, WithColumnFormatting, WithColumnWidths
{
    use Exportable;

    protected PostbackBuilder $query;

    public function __construct(PostbackBuilder $query)
    {
        $this->query = $query;
    }

    /**
    * @return PostbackBuilder
     */
    public function query()
    {
        return $this->query;
    }

    public function map($postback): array
    {
        /** @var Postback $postback */
        return [
            $postback->id,
            $postback->remote_user_id ?? $postback->user->id,
            $postback->user->webmaster?->source?->name ?? '-',
            $postback->user->webmaster?->api_id ?? '-',
            '`' .($postback->user->transaction_id ?? '-'),
            $postback->user->registerExtraData->site_id ?? '-',
            $postback->user->registerExtraData->place_id ?? '-',
            $postback->user->registerExtraData->banner_id ?? '-',
            $postback->user->registerExtraData->campaign_id ?? '-',
            $postback->cost,
            $postback->created_at->format('d.m.Y H:i:s'),
            $postback->sent_at === null ? 'Не отправлена' : 'Отправлена',
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'Идентификатор пользователя',
            'Партнерская программа',
            'Идентификатор вебмастера в ПП',
            'ID клика',
            'Site ID',
            'Place ID',
            'Banner ID',
            'Campaign ID',
            'Потрачено',
            'Создано',
            'Статус отправки в ПП',
        ];
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 2000;
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_GENERAL, // или другие варианты ниже
        ];
    }

    public function columnWidths(): array
    {
        return [
            'E' => 20,
        ];
    }
}
