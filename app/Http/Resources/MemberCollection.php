<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name'  => $this->name,
            'email'  => $this->email,
            'slug'  => $this->slug,
            'description'  => $this->description,
            'image'  => $this->image,
            'social_media'  => $this->social_media,
            'teams' => TeamCollection::collection($this->whenLoaded('teams')),
        ];
    }
}
