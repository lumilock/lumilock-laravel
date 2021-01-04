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
            'uri_name' => $this->uri_name,
            'secret_name' => $this->secret_name,
            'path' => $this->path,
            'picture_512' => $this->picture_512,
            'picture_256' => $this->picture_256,
            'picture_128' => $this->picture_128,
            'picture_64' => $this->picture_64,
            'picture_32' => $this->picture_32,
            'picture_16' => $this->picture_16,
            'created_at' => $this->created_at
        ];
    }
}
