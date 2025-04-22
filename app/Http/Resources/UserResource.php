<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "username" => $this->username,
            "email" => $this->email,
            'urlImageProfile' => $this->url_image_profile,
            "updated_at" => $this->updated_at->toDateTimeString(),
            "created_at" => $this->created_at->toDateTimeString(),
        ];
    }
}
