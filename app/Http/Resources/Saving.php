<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Saving extends JsonResource
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
            'amount' => $this->amount,
            'owner_id' => $this->owner_id,
            'completed' => $this->completed,
            'created_at' => $this->created_at,
            'target' => $this->target,
            'target_formatted' => number_format($this->target, 2),
            'cycle' => $this->cycle,
            'currency' => $this->owner->country->currency,
            'maturity' => $this->maturity,
            'matured' =>  $this->matured,
            'amount_saved' => $this->amount_saved,
            'total_contributions' => $this->total_contributions
        ];
    }
}
