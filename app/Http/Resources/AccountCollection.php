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
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'other_name' => $this->user->other_name,
            'address' => $this->address,
            'passport_photo_url' => '',
            'dob' => $this->dob,
            'gender' => $this->gender,
            'bank_account_number' => $this->bank_account_number,
            'bank_name' => $this->bank_name,
            'secondary_phone' => $this->secondary_phone,
            'next_of_kin_phone' => $this->next_of_kin_phone,
            'marital_status' => $this->marital_status,
            'lga' => $this->lga,
            'business_name' => $this->business_name,
            'business_address' => $this->business_address,
            'business_phone' => $this->business_phone,
            'agreement_form_url' => '',
            'emergency_name' => $this->emergency_name,
            'emergency_phone' => $this->emergency_phone,
            'next_of_kin_name' => $this->next_of_kin_name,
            'created_at' => $this->created_at,
            'account_number' => $this->user->account_number,
            'country' => $this->user->country,
            'roles' => $this->user->roles,
            'commission' => $this->commission / 100
        ];
    }
}
