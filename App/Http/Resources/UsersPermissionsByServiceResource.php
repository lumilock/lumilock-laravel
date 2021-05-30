<?php

namespace lumilock\lumilock\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsersPermissionsByServiceResource extends JsonResource
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
            'picture' => $this->picture,
            'permissions' => UserRightResource::collection($this->permissions)
        ];
    }
}
