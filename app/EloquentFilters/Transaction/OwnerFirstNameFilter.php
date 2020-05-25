<?php

namespace App\EloquentFilters\Transaction;

use Illuminate\Database\Eloquent\Builder;
/**
 * Class OwnerFirstNameFilter
 *
 * @package \App\EloquentFilters\Transaction
 */
class OwnerFirstNameFilter extends OwnerFilterBase
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
        return $this->query($builder, $value, 'first_name');
    }


}
