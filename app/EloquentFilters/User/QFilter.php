<?php

namespace App\EloquentFilters\User;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
/**
 * Class QFilter
 *
 * @package \App\EloquentFilters\Profile
 */
class QFilter extends Filter
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

        $fields = ['id', 'name', 'email', 'phone'];
        $query = $builder->where('id', 'LIKE', '%' . $value . '%');
        foreach($fields as $field)
        {
            $query->orWhere($field, 'LIKE', '%' . $value . '%');
        }

        return $query;

    }
}
