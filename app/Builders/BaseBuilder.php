<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

/**
 * @template TModelClass of Model
 *
 * @extends EloquentBuilder<TModelClass>
 */
abstract class BaseBuilder extends Builder
{
    /**
     * Проверяет, присоединена ли таблица.
     *
     * @param string $table
     *
     * @return bool
     */
    protected function joined(string $table): bool
    {
        // Несмотрят на PHPDoc, в joins может быть null
        $joins = $this->query->joins ?? []; // @phpstan-ignore-line

        /** @var JoinClause $join */
        foreach ($joins as $join) {
            if ($join->table === $table) {
                return true;
            }
        }

        return false;
    }
}