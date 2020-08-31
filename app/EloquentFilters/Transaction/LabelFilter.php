<?php

namespace App\EloquentFilters\Transaction;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class LabelFilter
 *
 * @package \App\EloquentFilters\Transaction
 */
class LabelFilter extends Filter
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
       return $builder->where('label', trim($value));
    }
}
