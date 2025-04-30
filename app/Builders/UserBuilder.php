<?php

namespace App\Builders;

use App\Enums\SmsTypeEnum;
use App\Models\Role;
use App\Models\Sms;
use App\Models\Source;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method null|User first($columns = ['*'])
 * @method null|User find($id, $columns = ['*'])
 * @method User findOrFail($id, $columns = ['*'])
 * @method User create(array $attributes = [])
 * @method UserBuilder filter($query, array $input = [], $filter = null)
 *
 * @template TModelClass of Model&User
 *
 * @extends BaseBuilder<User>
 */
class UserBuilder extends BaseBuilder
{
    /**
     * Фильтр по пользователям, которые не отключили подписку.
     *
     * @return UserBuilder
     */
    public function whereEnabled(): UserBuilder
    {
        return $this->where('is_disabled', 0);
    }

    /**
     * Фильтр по дате создания пользователя после переданной даты.
     *
     * @param CarbonInterface $date
     * @return UserBuilder
     */
    public function whereCreatedAtAfter(CarbonInterface $date): UserBuilder
    {
        return $this->where('created_at', '>=', $date);
    }

    /**
     * Фильтр по дате создания пользователя до переданной даты.
     *
     * @param CarbonInterface $date
     * @return UserBuilder
     */
    public function whereCreatedAtBefore(CarbonInterface $date): UserBuilder
    {
        return $this->where('created_at', '<=', $date);
    }

    /**
     * Фильтр по источнику.
     * @param int $sourceId
     * @return UserBuilder
     */
    public function whereSourceId(int $sourceId): UserBuilder
    {
        return $this->where(function (Builder $query) use ($sourceId) {
            $query->whereHas('webmaster', function (Builder $query) use ($sourceId) {
                $query->where('source_id', $sourceId);
            });

            if ($sourceId === Source::ID_DIRECT) {
                $query->orDoesntHave('webmaster');
            }
        });
    }

    /**
     * Фильтр по пользователям, которым можно отправить заданную SMS.
     * @param Sms $sms
     * @return UserBuilder
     */
    public function whereShouldReceiveSms(Sms $sms): UserBuilder
    {
        $isExcludedWebmastersFilled = $sms->excludedWebmasters()->exists();
        $isIncludedWebmastersFilled = $sms->includedWebmasters()->exists();

        return $this
            ->where('is_active', 1)
            ->whereCreatedAtAfter($sms->registered_after ?? $sms->created_at)
            ->whereNotNull('mphone')
            ->where('is_disabled', 0)
            ->whereDoesntHave('sms', function (Builder $query) use ($sms) {
                $query->where('id', $sms->id);
            })
            ->whereDoesntHave('failedSms')
            ->when($isExcludedWebmastersFilled, function (UserBuilder $query) use ($sms) {
                $query->whereNotIn('webmaster_id', $sms->excludedWebmasters()->select(['id']));
            })
            ->when($isIncludedWebmastersFilled, function (UserBuilder $query) use ($sms) {
                $query->whereIn('webmaster_id', $sms->includedWebmasters()->select(['id']));
            })
            ->when(
                $sms->type === SmsTypeEnum::AfterClick,
                function (UserBuilder $query) use ($sms) {
                    // Если SMS по касанию, то проверяем, что пользователь кликнул по SMS и это было не позже, чем $sms->delay минут назад
                    $query->whereHas('smsClicks', function (SmsClickBuilder $query) use ($sms) {
                        $query->where('sms_id', $sms->related_sms_id)
                            ->whereCreatedAtBefore(Carbon::now()->subMinutes($sms->delay));
                    });
                },
                function (UserBuilder $query) use ($sms) {
                    // Если SMS не по касанию, то проверяем, что пользователь зарегистрировался не позже, чем $sms->delay минут назад
                    $query->whereCreatedAtBefore(Carbon::now()->subMinutes($sms->delay));
                }
            );
    }

    /**
     * Фильтр по плану списаний.
     * @param int $paymentPlan
     * @return UserBuilder
     */
    public function wherePaymentPlan(int $paymentPlan): UserBuilder
    {
        return $this->where('payment_plan', $paymentPlan);
    }

    /**
     * Фильтр по пользователям, доступным текущему пользователю.
     *
     * @param User $user
     * @return UserBuilder
     */
    public function forUser(User $user): UserBuilder
    {
        if ($user->role_id === Role::ID_ADMIN || $user->isSuperAdmin() || $user->isSuperAdmin()) {
            return $this;
        }

        if ($user->role_id !== Role::ID_TRAFFIC_SOURCE) {
            return $this->whereRaw('0 = 1');
        }

        return $this->whereHas('webmaster', function (WebmasterBuilder $query) use ($user) {
            $query->forUser($user);
        });
    }

    /**
     * Поиск по пользователям, которые заполнили почту.
     * @return UserBuilder
     */
    public function whereHasRealEmail(): UserBuilder
    {
        return $this->where('email', 'not like', '%@' . config('app.domain'));
    }
}