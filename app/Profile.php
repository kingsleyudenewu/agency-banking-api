<?php

namespace App;



class Profile extends BaseModel
{
    protected $fillable = [
        'user_id',
        'home_address',
        'passport',
        'dob',
        'gender',
        'bank_account_number',
        'bank_name',
        'secondary_phone',
        'next_of_kin_phone',
        'marital_status',
        'lga',
        'state_id',
        'business_name',
        'business_address',
        'business_phone',
        'bvn',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
