<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SavingCycle extends JsonResource
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
            'title' => $this->title,
            'duration' => $this->duration,
            'description' => $this->description,
            'rule' => $this->rule,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'charge_type' => $this->charge_type,
            'min_saving_amount' => $this->min_saving_amount,
            'percentage_to_charge' => $this->percentage_to_charge,
            'min_saving_frequent' => $this->min_saving_frequent,
        ];
    }
}
