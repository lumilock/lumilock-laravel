<?php

namespace lumilock\lumilock\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthPermissionsResource extends JsonResource
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
            'path' => $this->path,
            'uri' => $this->uri,
            'permissions' => UserRightResource::collection($this->permissions)
        ];
    }
}
