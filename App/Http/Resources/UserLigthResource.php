<?php

namespace lumilock\lumilock\App\Http\Resources;

use App\Libraries\Helpers;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;


class UserLigthResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'picture' => $this->picture ? Helpers::asset(Storage::url("Users/$this->id/profile/$this->picture")) : "",
            'active' => $this->active,
        ];
    }
}
