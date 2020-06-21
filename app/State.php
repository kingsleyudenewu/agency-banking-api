<?php

namespace App;


class State extends BaseModel
{
    protected $fillable = ['name', 'country_id', 'enabled'];

    protected $hidden = ['country_id', 'enabled', 'created_at', 'updated_at', 'deleted_at'];

    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
