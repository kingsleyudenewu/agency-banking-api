<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;


/**
 * Class Money
 *
 * @package \App\Casts
 */
class Money implements CastsAttributes
{

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $key
     * @param mixed                               $value
     * @param array                               $attributes
     *
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $currency = $model->currency ? strtoupper($model->currency) : 'NGN';

        return money($value, $currency);
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $key
     * @param mixed                               $value
     * @param array                               $attributes
     *
     * @return array
     */
    public function set($model, string $key, $value, array $attributes)
    {
       return $value;

}}
