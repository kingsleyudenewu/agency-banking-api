<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BannedName extends Model
{
    protected $guarded = ['name'];

    /**
     * Get a list of all banned names
     *
     * @return array
     */
    public static function getNames()
    {
        return static::pluck('name')->toArray();
    }

    /**
     * All name value must be lowercase
     * @param $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }
}
