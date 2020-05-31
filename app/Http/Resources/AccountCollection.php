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
            'passport_photo_url' => '',
            'dob' => $this->dob,
            'gender' => $this->gender,
            'next_of_kin_name' => $this->next_of_kin_name,
            'next_of_kin_phone' => $this->next_of_kin_phone,
            'agreement_form_url' => '',
            'created_at' => $this->created_at,
            'account_number' => $this->user->account_number,
            'country' => $this->user->country,
            'roles' => $this->user->roles,
            'commission' => $this->commission / 100,
            'providus_account_number' => $this->user->providus_account_number
        ];
    }
}
