<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Wallet extends JsonResource
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
            'balance' => $this->amount,
            'balance_format' => number_format($this->amount, 2),
            'currency' => $this->currency,
            'type' => $this->type
        ];
    }
}
