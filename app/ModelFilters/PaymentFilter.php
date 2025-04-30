<?php

namespace App\ModelFilters;

use Carbon\Carbon;
use EloquentFilter\ModelFilter;
use Illuminate\Support\Str;

class PaymentFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [
        'user' => [
            'payment_plan',
        ],
    ];

    /**
     * @param string $value
     * @return void
     */
    public function dateFrom(string $value): void
    {
        $this->where('payments.created_at', '>=', Carbon::parse($value)->startOfDay());
    }

    /**
     * @param string $value
     * @return void
     */
    public function dateTo(string $value): void
    {
        $this->where('payments.created_at', '<=', Carbon::parse($value)->endOfDay());
    }

    /**
     * @param string $value
     * @return void
     */
    public function type(string $value): void
    {
        $this->where('payments.type', $value);
    }

    /**
     * @param string $value
     * @return void
     */
    public function status(string $value): void
    {
        $this->where('payments.status', $value);
    }

    /**
     * @param string $value
     * @return void
     */
    public function amount(string $value): void
    {
        $this->where('payments.amount', $value);
    }

    /**
     * @param string $value
     * @return void
     */
    public function paymentNumber(string $value): void
    {
        $this->where('payments.payment_number', (int)$value - 1);
    }

    /**
     * @param string $value
     * @return void
     */
    public function iterationNumber(string $value): void
    {
        $this->where('payments.iteration_number', (int)$value - 1);
    }

    /**
     * @param string $value
     * @return void
     */
    public function errorCode(string $value): void
    {
        $this->where('payments.error_code', $value);
    }

    public function cardNumber(string $value): void {
        $value = Str::replace('*', 'x', $value);

        $this->where('card_number', $value);
    }
}
