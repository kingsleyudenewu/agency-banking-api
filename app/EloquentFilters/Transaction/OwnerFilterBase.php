<?php

namespace App\EloquentFilters\Transaction;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;


/**
 * Class OwnerFilterBase
 *
 * @package \App\EloquentFilters\Transaction
 */
abstract class OwnerFilterBase extends  Filter
{

    /**
     * Apply the filter to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param mixed   $value
     *
     * @return Builder
     */
    abstract public function apply(Builder $builder, $value): Builder;

    protected function query(Builder $builder, $value, $field): Builder {

        return $builder->whereHas('owner', function (Builder $query) use ($value, $field){
            $query->where($field, $value);
        });
    }

}
