<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name Название
 * @property string $api_login Логин для доступа к API
 * @property string $api_password Пароль для доступа к API
 * @property int $service_id Тип сообщения
 * @property null|string $sender Отправитель
 * @property bool $is_for_activation Использовать для активации аккаунтов
 * @property float $sms_cost Стоимость одного SMS
 */
class SmsProvider extends Model
{
    use SoftDeletes;

    public const SERVICE_MY_SMPP = 1;
    public const SERVICE_SMS_RU = 2;

    public const SERVICES = [
        self::SERVICE_MY_SMPP => 'My SMPP',
        self::SERVICE_SMS_RU => 'SMS.ru',
    ];

    protected $casts = [
        'from_name' => 'array',
    ];

    public function setFromNameAttribute(array $va): void
    {
        $this->attributes['from_name'] = json_encode($va);
    }

    protected $fillable = [
        'name',
        'api_login',
        'api_password',
        'service_id',
        'sender',
        'is_for_activation',
        'sms_cost',
        'from_name'
    ];
}
