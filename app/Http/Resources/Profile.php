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
            'name' => $this->first_name . ' ' . $this->last_name,
            'account_number' => $this->account_number,
            'role' => $this->getRoles(),
            'wallets' => Wallet::collection($this->wallets)
        ];
    }


}
