<?php

namespace App;



use App\Casts\Json;

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
        'passport_photograph',
        'agreement_form',
        'emergency_phone',
        'emergency_name'
    ];

    protected $casts = [
        'passport_photograph' => Json::class,
        'agreement_form' => Json::class,
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * By default, a profile status is setting up
     *
     * @return bool
     */
    public function isSettingUp(): bool
    {
        return boolval($this->setup_completed) ? false :  true;
    }

    /**
     * Profile can be marked completed
     */
    public function setupCompleted()
    {
        $this->setup_completed = true;
        $this->save();

    }
}
