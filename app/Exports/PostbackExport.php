<?php

namespace App\Exports;

use App\Builders\PostbackBuilder;
use App\Models\Postback;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PostbackExport implements FromQuery, WithMapping, WithHeadings, WithCustomChunkSize
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
            $postback->user->transaction_id ?? '-',
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
}
