<?php

namespace App\EloquentFilters\User;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class StateIdFilter
 *
 * @package \App\EloquentFilters\User
 */
class StateIdFilter extends Filter
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
        return $builder->whereHas('profile', function($query) use ($value){
            return $query->where('state_id', $value);
        } );
    }
}
