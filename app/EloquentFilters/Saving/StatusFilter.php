<?php

namespace App\EloquentFilters\Saving;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class StatusFilter
 *
 * @package \App\EloquentFilters\Saving
 */
class StatusFilter extends Filter
{

    const STATUS_ACTIVE = 'active';
    const  STATUS_NOT_ACTIVE = 'not-active';
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
        $value = strtolower(trim($value));

        if(static::STATUS_ACTIVE === $value) {
            return $builder->whereNull('completed');
        } elseif(static::STATUS_NOT_ACTIVE === $value) {
            return $builder->whereNotNull('completed');
        }

        return $builder;
    }
}
