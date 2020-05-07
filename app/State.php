<?php

namespace App;


class State extends BaseModel
{
    protected $fillable = ['name', 'country_id'];

    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
