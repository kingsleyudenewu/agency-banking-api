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
            'id' => $this->user->id,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'name' => $this->user->name,
            'address' => $this->address,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'next_of_kin_name' => $this->next_of_kin_name,
            'next_of_kin_phone' => $this->next_of_kin_phone,
            'created_at' => $this->created_at,
            'account_number' => $this->user->account_number,
            'country' => $this->user->country,
            'roles' => $this->user->roles,
            'commission' => $this->commission,
            'commission_for_agent' => $this->commission_for_agent,
            'providus_account_number' => $this->user->providus_account_number,
            'agreement_form_url' => $this->agreement_form_url,
            'means_of_identification_url' => $this->means_of_identification_url,
            'application_form_url' => $this->application_form_url,
            'wallets' => $this->user->wallets
        ];
    }
}
