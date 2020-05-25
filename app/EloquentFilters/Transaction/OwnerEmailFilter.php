<?php

namespace App\EloquentFilters\Transaction;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;


/**
 * Class OwnerEmailFilter
 *
 * @package \App\EloquentFilters\Transaction
 */
class OwnerEmailFilter extends OwnerFilterBase
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
       return $this->query($builder, $value, 'email');
    }
}
