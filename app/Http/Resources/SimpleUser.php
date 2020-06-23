<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
/**
 * Class SimpleUser
 *
 * @package \App\Http\Resources
 */
class SimpleUser extends JsonResource
{
    public function toArray($request) : array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone
        ];
    }
}
