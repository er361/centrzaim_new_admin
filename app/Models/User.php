<?php

namespace App\Models;

use App\Builders\UserBuilder;
use App\Repositories\UserRepository;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @property int $payment_plan Идентификатор плана, по которому идут списания у пользователя
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $remember_token
 * @property string $last_name
 * @property string $logged_at
 * @property null|int $fill_status Статус заполнения анкеты пользователя (берется из констант класса)
 * @property string $first_name
 * @property string $middlename
 * @property integer $credit_sum
 * @property integer $credit_days
 * @property string $phone
 * @property string $mphone
 * @property string $birthdate
 * @property string $birthplace
 * @property string $citizenship
 * @property integer $gender
 * @property integer $reg_permanent
 * @property string $reg_region_name
 * @property string $reg_city_name
 * @property string $reg_street
 * @property string $reg_house
 * @property string $reg_flat
 * @property string $fact_country_name
 * @property string $fact_region_name
 * @property string $fact_city_name
 * @property string $fact_street
 * @property string $fact_house
 * @property string $fact_flat
 * @property string $work_experience
 * @property string $passport_title
 * @property string $passport_date
 * @property string $passport_code
 * @property null|string $activation_code Код активации аккаунта (отправляется на мобильный телефон)
 * @property bool $is_active Активирован ли аккаунт по смс
 * @property null|string $transaction_id Номер транзакции на стороне партнерской системы
 * @property null|string $additional_transaction_id Дополнительный номер транзакции на стороне партнерской системы
 * @property null|bool $is_disabled Отписался ли пользователь
 * @property null|int $role_id Идентификатор роли пользователя
 * @property null|string $geo_region Регион пользователя
 * @property null|string $geo_city Город пользователя
 * @property null|int $webmaster_id
 * @property null|string $ip_address IP Адрес пользователя
 * @property bool $is_payment_required Показывать ли форму оплаты пользователю
 * @property int $recurrent_payment_consequent_error_count Количество последовательных ошибок при списании рекуррентных платежей
 * @property int $recurrent_payment_success_count Количество успешных рекуррентных платежей
 * @property-read int $id Идентификатор пользователя
 * @property-read string $unique_id Уникальный между сайтами идентификатор пользователя
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 *
 * @property-read null|Webmaster $webmaster
 * @property-read Collection<int, Payment> $payments
 * @property-read null|Payment $latestRecurrentPayment Последний рекуррентный платеж
 * @property-read null|Payment $latestRecurrentIterationStartPayment Последний рекурретный платеж, являющийся стартом итерации
 * @property-read Collection<int, LeadService> $leadServices Отправленные анкеты по пользователю
 * @property-read Collection<int, Webmaster> $accessibleWebmasters Вебмастера, к статистике по которым есть доступ
 * @property-read Collection<int, Postback> $postbacks Отправленные постбэки
 * @property-read Collection<int, SmsClick> $smsClicks Клики по отправленным SMS
 * @property null| UserOffer  $offers
 *
 * @method static UserBuilder query()
 * @method static UserFactory factory()
 */
class User extends Authenticatable
{
    use Notifiable;
    use Filterable;
    use HasFactory;
    use HasApiTokens;

    /**
     * Статус заполнения анкеты - завершил заполнение анкеты.
     */
    public const FILL_STATUS_FINISHED = -1;


    public function isAdmin()
    {
        return $this->role->id === Role::ID_ADMIN;
    }

    public function isSuperAdmin()
    {
        return $this->role->id === Role::ID_SUPER_ADMIN;
    }

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'remember_token',
        'last_name',
        'logged_at',
        'fill_status',
        'first_name',
        'middlename',
        'credit_sum',
        'credit_days',
        'phone',
        'mphone',
        'birthdate',
        'birthplace',
        'citizenship',
        'gender',
        'reg_permanent',
        'reg_region_name',
        'reg_city_name',
        'reg_street',
        'reg_house',
        'reg_flat',
        'fact_country_name',
        'fact_region_name',
        'fact_city_name',
        'fact_street',
        'fact_house',
        'fact_flat',
        'work_experience',
        'passport_title',
        'passport_date',
        'passport_code',
        'role_id',
        'comment',
        'is_active',
        'activation_code',
        'ip_address',
        'is_disabled',
        'webmaster_id',
        'transaction_id',
        'additional_transaction_id',
        'payment_plan',
        'geo_city',
        'geo_region',
        'is_payment_required',
        'recurrent_payment_consequent_error_count',
        'recurrent_payment_success_count',
        'unsubscribed_at'
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_payment_required' => 'bool',
    ];

    /**
     * @param $query
     * @return UserBuilder
     */
    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }

    /**
     * @return BelongsTo
     */
    public function webmaster(): BelongsTo
    {
        return $this->belongsTo(Webmaster::class);
    }

    /**
     * Вебмастера, к статистике по которым есть доступ.
     *
     * @return BelongsToMany
     */
    public function accessibleWebmasters(): BelongsToMany
    {
        return $this->belongsToMany(Webmaster::class, 'user_accessible_webmaster');
    }

    /**
     * @return HasMany<Payment>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    /**
     * @return HasOne
     */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'user_id')
            ->latestOfMany();
    }

    public function offers(): HasOne
    {
        return $this->hasOne(UserOffer::class);
    }

    /**
     * @return HasOne
     */
    public function latestRecurrentIterationStartPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'user_id')
            ->ofMany([
                'id' => 'max',
            ], function ($query) {
                $query
                    ->where('status', Payment::STATUS_PAYED)
                    ->where('type', Payment::TYPE_RECURRENT)
                    ->where('payment_number', 0);
            });
    }

    public function calculatePaymentSubType():int
    {
        if($this->is_disabled) {
            return -1;
        }

        $isMonthlyFailed = $this->payments()->whereSubtype(Payment::SUBTYPE_MONTHLY)
            ->whereStatusFailed()
            ->exists();

        $hasAnyPayments = $this->payments()
            ->whereIn('subtype',Payment::SUBTYPES)
            ->exists();

        if(!$hasAnyPayments) {
            return Payment::SUBTYPE_MONTHLY;
        }

        if($isMonthlyFailed) {
            return Payment::SUBTYPE_WEEKLY;
        }

        return Payment::SUBTYPE_MONTHLY;
    }

    /**
     * @return HasOne
     */
    public function latestRecurrentPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'user_id')
            ->ofMany([
                'id' => 'max',
            ], function ($query) {
                $query
                    ->where('type', Payment::TYPE_RECURRENT);
            });
    }

    /**
     * @return BelongsToMany
     */
    public function sms(): BelongsToMany
    {
        return $this->belongsToMany(Sms::class, 'sms_user')
            ->using(SmsUser::class);
    }

    /**
     * @return BelongsToMany
     */
    public function failedSms(): BelongsToMany
    {
        return $this->sms()
            ->wherePivotIn('status', [SmsUser::STATUS_FAILED, SmsUser::STATUS_SENDING_FAILED]);
    }

    /**
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function fccpInfo()
    {
        return $this->hasOne(Fccp::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function postbacks(): HasMany
    {
        return $this->hasMany(Postback::class);
    }

    public function registerExtraData()
    {
        return $this->hasOne(UserExtraData::class, 'user_id');
    }

    /**
     * @return BelongsToMany|Builder
     */
    public function leadServices(): BelongsToMany|Builder
    {
        return $this->belongsToMany(LeadService::class)
            ->withPivot('error_message')
            ->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function smsClicks(): HasMany
    {
        return $this->hasMany(SmsClick::class);
    }

    /**
     * @return HasOne
     */
    public function action(): HasOne
    {
        return $this->hasOne(Action::class, 'api_transaction_id', 'transaction_id')
            ->where('actions.webmaster_id', $this->webmaster_id);
    }

    /**
     * Hash password
     * @param mixed $input
     */
    public function setPasswordAttribute(mixed $input)
    {
        $this->attributes['password'] = $input;
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    /**
     * Получить уникальный (между сайтами) идентификатор пользователя.
     * @return string
     */
    public function getUniqueIdAttribute(): string
    {
        return config('postbacks.adv_sub_prefix') . $this->id;
    }

    public function getPaymentPlan()
    {
        return $this->payment_plan;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        $name = $this->last_name . ' ' . $this->name;

        if (!empty($this->middlename)) {
            $name .= ' ' . $this->middlename;
        }

        return $name;
    }

    public function getFirstNameAttribute()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFullRegAddressAttribute()
    {
        return $this->reg_region_name . ', ' . $this->reg_city_name . ', ' . $this->reg_street . ', ' . $this->reg_house . ', ' . $this->reg_flat;
    }

    public function getFullFactAddressAttribute()
    {
        return $this->fact_region_name . ', ' . $this->fact_city_name . ', ' . $this->fact_street . ', ' . $this->fact_house . ', ' . $this->fact_flat;
    }

    /**
     * Получить действительный email пользователя, или null, если он его не сохранял.
     * На некоторых сервисах мы генерируем email на первом шаге регистрации.
     * @return null|string
     */
    public function getRealEmail(): ?string
    {
        return Str::endsWith($this->email, '@' . config('app.domain'))
            ? null
            : $this->email;
    }
}
