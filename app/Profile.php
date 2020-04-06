<?php

namespace App;



class Profile extends BaseModel
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
