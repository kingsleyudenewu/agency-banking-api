<?php

namespace App;

class Country extends BaseModel
{
    public $timestamps = false;

    protected $fillable = ['name', 'code', 'currency'];


    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }

    public function states()
    {
        return $this->hasMany(State::class);
    }
}
