<?php

namespace App;



use App\Casts\Json;
use Illuminate\Support\Facades\Storage;

class Profile extends BaseModel
{
    protected $fillable = [
        'user_id',
        'address',
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

    protected $appends = [
        'agreement_form_url',
        'means_of_identification_url',
        'application_form_url'
    ];

    protected $hidden  = [
        'agreement_form',
        'means_of_identification',
        'application_form'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function aetCommissionAttribute($value)
    {
        return $value / 100;
    }

    public function aetCommissionForAgentAttribute($value)
    {
        return $value / 100;
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

    public function getAgreementFormUrlAttribute($value)
    {
         $data = json_decode($this->agreement_form);

         return $data ?  $this->getDocUrl($data->disk, $data->path) : null;
    }


    public function getMeansOfIdentificationUrlAttribute($value)
    {
        $data = json_decode($this->means_of_identification);

        return $data ?  $this->getDocUrl($data->disk, $data->path) : null;
    }

    public function getApplicationFormUrlAttribute($value)
    {
        $data = json_decode($this->application_form);

        return $data ?  $this->getDocUrl($data->disk, $data->path) : null;
    }


    private function getDocUrl($disk, $path)
    {
        $path = str_replace('//', '/', $path);
        return Storage::disk($disk)->url($path);
    }

    public function completeSetup()
    {
        $this->setup_completed = true;
        $this->save();
    }
}
