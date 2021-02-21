<?php

namespace lumilock\lumilock\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
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
            'user_id' => $this->user_id,
            'expires_at' => $this->expires_at,
            'token' => substr($this->token, 0, 6) . '******' . substr($this->token, -6),
            // 'token2' => $this->token,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at
        ];
    }
}
