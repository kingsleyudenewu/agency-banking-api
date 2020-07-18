<?php

namespace App\EloquentFilters\Saving;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class MaturedFilter
 *
 * @package \App\EloquentFilters\Saving
 */
class MaturedFilter extends Filter
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
        if($value) return $builder->whereDate('maturity', '<', now());
        return $builder->whereDate('maturity', '>=', now());
    }
}
