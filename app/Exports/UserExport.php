<?php

namespace App\Exports;

use App\Builders\UserBuilder;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromQuery, WithMapping, WithHeadings, WithCustomChunkSize
{
    use Exportable;

    protected UserBuilder $query;

    public function __construct(UserBuilder $query)
    {
        $this->query = $query;
    }

    /**
    * @return UserBuilder
     */
    public function query()
    {
        return $this->query;
    }

    public function map($user): array
    {
        /** @var User $user */
        return [
            $user->id,
            $user->webmaster->source->name ?? '-',
            $user->webmaster->api_id ?? '-',
            $user->created_at->format('d.m.Y'),
            $user->last_name,
            $user->first_name,
            $user->middlename,
            $user->email,
            $user->mphone,
            $user->birthdate,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'Партнерская программа',
            'Вебмастер',
            'Дата регистрации',
            'Фамилия',
            'Имя',
            'Отчество',
            'Email',
            'Телефон',
            'Дата рождения',
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
