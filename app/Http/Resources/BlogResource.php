<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            "slug" => $this->slug,
            "title"=> $this->title,
            "content"=> $this->content,
            "urlImage"=> $this->url_image,
            "userId"=> $this->user_id,
            "updatedAt"=> $this->updated_at->toDateTimeString(),
            "createdAt"=> $this->created_at->toDateTimeString(),
        ];
    }
}
