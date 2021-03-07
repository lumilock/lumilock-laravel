<?php

namespace lumilock\lumilock\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use lumilock\lumilock\App\Models\Permission;
use lumilock\lumilock\App\Models\Service;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $permission = Permission::find($this->permission_id);
        return [
            'id' => $this->id,
            'name' => $permission->name,
            'service_name' => Service::find($permission->service_id)->name,
            'is_active' => $this->is_active,
        ];
    }
}
