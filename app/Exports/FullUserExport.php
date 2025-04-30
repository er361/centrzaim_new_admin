<?php

namespace App\Exports;

use App\Builders\UserBuilder;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FullUserExport implements FromQuery, WithMapping, WithHeadings, WithCustomChunkSize
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
            $user->name,
            $user->email,
            // Не включаем пароль по соображениям безопасности
            $user->remember_token,
            $user->created_at ? $user->created_at->format('d.m.Y H:i:s') : null,
            $user->updated_at ? $user->updated_at->format('d.m.Y H:i:s') : null,
            $user->role_id,
            $user->last_name,
            $user->logged_at ? date('d.m.Y H:i:s', strtotime($user->logged_at)) : null,
            $user->middlename,
            $user->credit_sum,
            $user->credit_days,
            $user->phone,
            $user->birthdate ? date('d.m.Y', strtotime($user->birthdate)) : null,
            $user->birthplace,
            $user->citizenship,
            $user->gender,
            $user->reg_permanent,
            $user->reg_region_name,
            $user->reg_city_name,
            $user->reg_street,
            $user->reg_house,
            $user->reg_flat,
            $user->fact_country_name,
            $user->fact_region_name,
            $user->fact_city_name,
            $user->fact_street,
            $user->fact_house,
            $user->fact_flat,
            $user->work_experience,
            $user->passport_title,
            $user->passport_date ? date('d.m.Y', strtotime($user->passport_date)) : null,
            $user->passport_code,
            $user->comment,
            $user->first_name,
            $user->is_active ? 'Да' : 'Нет',
            // Не включаем activation_code по соображениям безопасности
            $user->webmaster_id,
            $user->mphone,
            $user->ip_address,
            $user->is_payment_email_required ? 'Да' : 'Нет',
            $user->transaction_id,
            $user->additional_transaction_id,
            $user->is_disabled ? 'Да' : 'Нет',
            $user->unsubscribed_at ? date('d.m.Y H:i:s', strtotime($user->unsubscribed_at)) : null,
            $user->fill_status,
            $user->payment_plan,
            $user->geo_region,
            $user->geo_city,
            $user->is_payment_required ? 'Да' : 'Нет',
            $user->recurrent_payment_consequent_error_count,
            $user->recurrent_payment_success_count,
            // Дополнительно из исходного класса
            $user->webmaster->source->name ?? '-',
            $user->webmaster->api_id ?? '-',
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Имя пользователя',
            'Email',
            'Токен запоминания',
            'Дата создания',
            'Дата обновления',
            'ID роли',
            'Фамилия',
            'Последний вход',
            'Отчество',
            'Сумма кредита',
            'Дни кредита',
            'Телефон',
            'Дата рождения',
            'Место рождения',
            'Гражданство',
            'Пол',
            'Постоянная регистрация',
            'Регион регистрации',
            'Город регистрации',
            'Улица регистрации',
            'Дом регистрации',
            'Квартира регистрации',
            'Страна фактического проживания',
            'Регион фактического проживания',
            'Город фактического проживания',
            'Улица фактического проживания',
            'Дом фактического проживания',
            'Квартира фактического проживания',
            'Опыт работы',
            'Паспорт - наименование',
            'Паспорт - дата выдачи',
            'Паспорт - код',
            'Комментарий',
            'Имя',
            'Активен',
            'ID вебмастера',
            'Мобильный телефон',
            'IP адрес',
            'Требуется email для оплаты',
            'Номер транзакции',
            'Дополнительный номер транзакции',
            'Отключен',
            'Дата отписки',
            'Статус заполнения',
            'План оплаты',
            'Регион (гео)',
            'Город (гео)',
            'Требуется оплата',
            'Количество ошибок рекуррентных платежей',
            'Количество успешных рекуррентных платежей',
            'Партнерская программа',
            'Вебмастер',
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