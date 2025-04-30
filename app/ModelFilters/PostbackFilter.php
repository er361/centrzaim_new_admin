<?php

namespace App\ModelFilters;

use Carbon\Carbon;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class PostbackFilter extends ModelFilter
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
    public function source(string $value): void
    {
        $this->whereHas('user', function (Builder $query) use ($value) {
            $query->whereHas('webmaster', function (Builder $query) use ($value) {
                $query->where('source_id', $value);
            });
        });
    }

    /**
     * @param string $value
     * @return void
     */
    public function dateFrom(string $value): void
    {
        $this->where('created_at', '>=', Carbon::parse($value)->startOfDay());
    }

    /**
     * @param string $value
     * @return void
     */
    public function dateTo(string $value): void
    {
        $this->where('created_at', '<=', Carbon::parse($value)->endOfDay());
    }
}
