<?php

namespace lumilock\lumilock\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRightResource extends JsonResource
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
            'name' => $this->name,
            'is_active' => $this->users[0]->is_active,
        ];
    }
}
