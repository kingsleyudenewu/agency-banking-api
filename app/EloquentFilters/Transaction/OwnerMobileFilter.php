<?php

namespace App\EloquentFilters\Transaction;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class OwnerMobileFilter
 *
 * @package \App\EloquentFilters\Transaction
 */
class OwnerMobileFilter extends OwnerFilterBase
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
        return $builder->whereHas('owner', function (Builder $query) use ($value){
        $query->where('phone', 'LIKE', '%' . $value . '%');
    });
    }
}
