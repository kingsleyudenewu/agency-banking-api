<?php

namespace App\EloquentFilters\Transaction;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class OwnerLastNameFilter
 *
 * @package \App\EloquentFilters\Transaction
 */
class OwnerLastNameFilter extends OwnerFilterBase
{

    /**
     * Apply the filter to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param mixed   $value
     *
     * @return Builder
     */
    public function apply(Builder $builder, $value): Builder
    {
        return $this->query($builder, $value, 'last_name');
    }
}
