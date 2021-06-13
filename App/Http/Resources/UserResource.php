<?php

namespace lumilock\lumilock\App\Http\Resources;

use App\Libraries\Helpers;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $pictureUrl = filter_var($this->picture, FILTER_VALIDATE_URL);

        return [
            'id' => $this->id,
            'login' => $this->login,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'picture' => $this->picture && !$pictureUrl ? Helpers::asset(Storage::url("Users/$this->id/profile/$this->picture")) : $this->picture,
            'active' => $this->active,
            'created_at' => $this->created_at
        ];
    }
}
