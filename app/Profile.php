<?php

namespace App;



use App\Casts\Json;

class Profile extends BaseModel
{
    protected $fillable = [
        'user_id',
        'address',
        'means_of_identification',
        'dob',
        'gender',
        'bank_account_number',
        'bank_id',
        'secondary_phone',
        'next_of_kin_phone',
        'next_of_kin_name',
        'marital_status',
        'lga',
        'state_id',
        'business_name',
        'business_address',
        'business_phone',
        'business_type',
        'bvn',
        'agreement_form',
        'application_form',
        'emergency_phone',
        'emergency_name',
        'means_of_identification',
        'has_bank_account',
        'commission',
        'commission_for_agent'
    ];

    protected $casts = [
        'application_form' => Json::class,
        'agreement_form' => Json::class,
        'means_of_identification' => Json::class,
    ];

    protected $hidden  = [];

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
