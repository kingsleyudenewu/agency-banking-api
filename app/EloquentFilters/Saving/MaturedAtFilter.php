<?php

namespace App\EloquentFilters\Saving;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
/**
 * Class MaturedAtFilter
 *
 * @package \App\EloquentFilters\Saving
 */
class MaturedAtFilter extends Filter
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
      $parts = explode('-', $value);

      if($parts >= 3) {
          return $builder->whereYear('maturity', $parts[0])
                ->whereMonth('maturity', $parts[1])
                ->whereDay('maturity', $parts[2]);
      }

      return $builder;

    }
}
