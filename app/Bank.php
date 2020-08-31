<?php

namespace App;

class Bank extends BaseModel
{

    protected $fillable = ['code', 'name'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function scopeOrderByName($query)
    {
        return $query->orderBy('name');
    }

    public function scopeEnabled($query)
    {
        return $query->whereNotNull('enabled');
    }
}
