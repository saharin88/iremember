<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MemorialResource extends JsonResource
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
            'location_id' => $this->location_id,
            'image' => $this->image ? Storage::url($this->image) : null,
            'date' => $this->date,
            'people_count' => $this->people_count,

            'location' => LocationResource::make($this->whenLoaded('location')),
            'people' => PersonResource::collection($this->whenLoaded('people')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
        ];

        return $data;
    }
}
