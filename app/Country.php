<?php

namespace App;

class Country extends BaseModel
{
    public $timestamps = false;

    protected $fillable = ['name', 'code', 'currency'];
}
