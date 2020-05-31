<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProvidusTransaction extends Model
{
    protected $fillable = ['payload', 'ref', 'completed'];

    public  function isCompleted()
    {
        return boolval($this->completed);
    }


}
