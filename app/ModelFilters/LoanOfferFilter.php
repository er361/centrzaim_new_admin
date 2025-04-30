<?php

namespace App\ModelFilters;

use Carbon\Carbon;
use EloquentFilter\ModelFilter;

class LoanOfferFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    /**
     * @param string $value
     * @return void
     */
    public function type(string $value): void
    {
        $this->where('type', $value);
    }

    /**
     * @param string $value
     * @return void
     */
    public function sourceId(string $value): void
    {
        $this->where('source_id', $value);
    }
}
