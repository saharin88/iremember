<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'koatuu' => $this->koatuu,
            'katottg' => $this->katottg,
            'parent_id' => $this->parent_id,
            'people_birth_count' => $this->people_birth_count,
            'people_death_count' => $this->people_death_count,
            'people_burial_count' => $this->people_burial_count,
            'people_wound_count' => $this->people_wound_count,

            'parent' => self::make($this->whenLoaded('parent')),
            'ancestors' => self::make($this->whenLoaded('ancestors')),
            'children' => self::collection($this->whenLoaded('children')),
            'descendants' => self::collection($this->whenLoaded('descendants')),

            'images' => ImageResource::collection($this->whenLoaded('images')),
        ];

        return $data;
    }
}
