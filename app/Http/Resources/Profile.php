<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Profile extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'account_number' => $this->account_number,
            'status' => $this->status,
            'country' => $this->country,
            'roles' => $this->roles,
            'wallets' => $this->wallets,
            'next_of_kin_name' => $this->profile->next_of_kin_name,
            'next_of_kin_phone' => $this->profile->next_of_kin_phone,
            'address' => $this->profile->address,
            'business_address' => $this->profile->business_address,
            'dob' => $this->profile->dob,
            'gender' => $this->profile->gender,
            'commission' => $this->profile->commission,
            'commission_for_agent' => $this->profile->commission_for_agent,
            'agreement_form_url' => $this->profile->agreement_form_url,
            'means_of_identification_url' => $this->profile->means_of_identification_url,
            'application_form_url' => $this->profile->application_form_url,
            'bvn' => $this->profile->bvn,
            'business_phone' => $this->profile->business_phone,
            'business_type' => $this->profile->business_type,
            'state' => $this->profile->state,
            'is_suspended' => $this->is_suspended,
        ];
    }


}
