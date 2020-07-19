<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) : array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'account_number' => $this->account_number,
            'providus_account_number' => $this->providus_account_number,
            'roles' => $this->getRoles(),
            'country' => $this->country,
            'wallets' => $this->wallets,
            'has_transaction_pin' => $this->transaction_pin ? true : false,
            'transaction_auth_mode' => settings('transaction_auth') === 'pin' ? 'PIN' : 'SMS/Email OTP',
        ];
    }
}
