<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

            return [
                'id' => $this->id,
                'email' => $this->email,
                'phone' => $this->phone,
                'name' => $this->name,
                'address' => $this->profile->address,
                'dob' => $this->profile->dob,
                'gender' => $this->profile->gender,
                'next_of_kin_name' => $this->profile->next_of_kin_name,
                'next_of_kin_phone' => $this->profile->next_of_kin_phone,
                'created_at' => $this->created_at,
                'account_number' => $this->account_number,
                'country' => $this->country,
                'roles' => $this->roles,
                'commission' => $this->profile->commission,
                'commission_for_agent' => $this->profile->commission_for_agent,
                'providus_account_number' => $this->providus_account_number,
                'agreement_form_url' => $this->profile->agreement_form_url,
                'means_of_identification_url' => $this->profile->means_of_identification_url,
                'application_form_url' => $this->profile->application_form_url,
                'wallets' => $this->wallets,
                'state' => $this->profile->state,
                'occupation' => $this->profile->occupation,
            ];


    }
}
