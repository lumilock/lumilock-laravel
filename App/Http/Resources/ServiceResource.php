<?php

namespace lumilock\lumilock\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'uri' => $this->uri,
            'secret' => $this->secret,
            'path' => $this->path,
            'picture' => $this->picture,
            'address' => $this->address,
            'created_at' => $this->created_at
        ];
    }
}
