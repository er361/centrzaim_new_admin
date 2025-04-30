<?php

namespace App\Models;

use App\Builders\PaymentBuilder;
use Carbon\CarbonInterface;
use Database\Factories\PaymentFactory;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

/**
 * Class Payment
 *
 * @package App
 * @property int $service Платежная система
 * @property int $user_id Пользователь
 * @property int $amount Сумма платежа
 * @property int $type Тип платежа
 * @property int $status Статус платежа
 * @property string $rebill_id Идентификатор для повторного списания (рекурентные платежи)
 * @property string $access_key Уникальный ключ для получения информации о платеже
 * @property float $commission Комиссия за обработку платежа
 * @property null|string $error_code Код ошибки
 * @property null|int $iteration_number Порядковый номер итерации
 * @property null|int $payment_number Порядковый номер платежа в рамках итерации
 * @property null|string $card_number Номер карты, используемый при платеже
 *
 * @property-read int $id
 * @property-read User $user Пользователь, к которому привязан платеж
 * @property-read string $status_description Описание статуса
 * @property-read string $type_description
 * @property-read string $subtype_description
 * @property-read string $service_description
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 *
 * @method static PaymentBuilder query()
 * @method static PaymentFactory factory($count = null, $state = [])
 */
class Payment extends Model
{
    use HasFactory;
    use Filterable;

    /**
     * Оплата через Тинькофф.
     */
    public const SERVICE_TINKOFF = 1;

    /**
     * Оплата через Impaya.
     */
    public const SERVICE_IMPAYA = 2;

    /**
     * Обычный платеж.
     */
    public const TYPE_DEFAULT = 1;

    /**
     * Рекурретный платеж.
     */
    public const TYPE_RECURRENT = 2;

    /**
     * Типы платежа.
     */
    public const TYPES = [
        self::TYPE_DEFAULT => 'Обычный',
        self::TYPE_RECURRENT => 'Рекуррентный',
    ];

    public const SUBTYPE_MONTHLY = 1;
    public const SUBTYPE_WEEKLY = 2;
    public const SUBTYPE_RETRY_AFTER_FAILED_MONTHLY = 3;

    public const SUBTYPES = [
        self::SUBTYPE_MONTHLY => 1,
        self::SUBTYPE_WEEKLY => 2,
        self::SUBTYPE_RETRY_AFTER_FAILED_MONTHLY => 3,
    ];

    /**
     * Платеж создан.
     */
    public const STATUS_CREATED = 0;

    /**
     * Создан запрос на привязку карты.
     */
    public const STATUS_ADD_CARD_CREATED = 1;

    /**
     * Платеж оплачен.
     */
    public const STATUS_PAYED = 10;

    /**
     * Платеж отклонен.
     */
    public const STATUS_DECLINED = 11;

    /**
     * По платежу удалось успешно привязать карту.
     */
    public const STATUS_CARD_ADDED = 12;

    /**
     * Статусы и их обозначения.
     */
    public const STATUSES = [
        self::STATUS_CREATED => 'Создан',
        self::STATUS_ADD_CARD_CREATED => 'Создан запрос на привязку карты',
        self::STATUS_PAYED => 'Оплачен',
        self::STATUS_CARD_ADDED => 'Карта привязана',
        self::STATUS_DECLINED => 'Отклонен',
    ];

    /**
     * Длина ключа доступа.
     */
    public const ACCESS_KEY_LENGTH = 64;

    /**
     * Символ маскирования номера карты.
     */
    public const CARD_MASK_SYMBOL = 'x';

    protected $fillable = [
        'service',
        'amount',
        'type',
        'status',
        'rebill_id',
        'user_id',
        'subtype',
        'access_key',
        'commission',
        'error_code',
        'payment_number',
        'iteration_number',
        'card_number',
    ];

    protected $hidden = [
        'access_key',
    ];

    /**
     * @param $query
     * @return PaymentBuilder
     */
    public function newEloquentBuilder($query): PaymentBuilder
    {
        return new PaymentBuilder($query);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Получить текстовое обозначение статуса.
     * @return null|string
     */
    public function getStatusDescriptionAttribute(): ?string
    {
        return Arr::get(self::STATUSES, $this->status);
    }



    /**
     * Получить текстовое обозначение сервиса.
     * @return string|null
     */
    public function getServiceDescriptionAttribute(): ?string
    {
        $services = [
            self::SERVICE_TINKOFF => 'Тинькофф',
            self::SERVICE_IMPAYA => 'Impaya',
        ];

        return $services[$this->service] ?? null;
    }

    /**
     * Получить текстовое обозначение типа.
     * @return string|null
     */
    public function getTypeDescriptionAttribute(): ?string
    {
        if ($this->type === null) {
            return null;
        }

        return Arr::get(self::TYPES, $this->type);
    }

    public function getSubtypeDescriptionAttribute(): ?string
    {
        return match ($this->subtype) {
            self::SUBTYPE_MONTHLY => 'Ежемесячный',
            self::SUBTYPE_WEEKLY => 'Еженедельный',
            self::SUBTYPE_RETRY_AFTER_FAILED_MONTHLY => 'Повторный после неудачного ежемесячного',
            default => null,
        };
    }

}
