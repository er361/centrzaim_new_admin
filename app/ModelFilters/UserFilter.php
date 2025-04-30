<?php

namespace App\ModelFilters;

use App\Builders\UserBuilder;
use App\Models\User;
use Carbon\Carbon;
use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin UserBuilder
 */
class UserFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    /**
     * @var string[]
     */
    protected $blacklist = ['role'];

    /**
     * Настройка фильтра.
     * @return void
     */
    public function setup(): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->can('user_create')) {
            $this->whitelistMethod('role');
        }
    }

    /**
     * Фильтр по вебмастеру.
     * @param $value
     * @return void
     */
    public function webmaster($value): void
    {
        $this->where('webmaster_id', $value);
    }

    /**
     * Фильтр по датам создания пользователя.
     * @param $value
     * @return void
     */
    public function createdAtDates($value): void
    {
        $values = explode(' - ', $value);

        if (isset($values[0])) {
            $this->where('created_at', '>=', Carbon::parse($values[0])->startOfDay());
        }

        if (isset($values[1])) {
            $this->where('created_at', '<=', Carbon::parse($values[1])->endOfDay());
        }
    }

    /**
     * Фильтр по email.
     * @param string $value
     * @return void
     */
    public function email(string $value): void
    {
        $this->where('email', $value);
    }

    /**
     * Фильтр по email.
     * @param string $value
     * @return void
     */
    public function mphone(string $value): void
    {
        $this->where(function (UserBuilder $query) use ($value) {
            // Разрешаем передавать как без +, так и с ним
            $query
                ->where('mphone', $value)
                ->orWhere('mphone', '+' . $value);
        });
    }

    /**
     * Фильтр по email.
     * @param string $value
     * @return void
     */
    public function paymentPlan(string $value): void
    {
        $this->where('payment_plan', $value);
    }

    /**
     * Фильтр по роли.
     * @param string $value
     * @return void
     */
    public function role(string $value): void
    {
        $this->where('role_id', $value);
    }
}
