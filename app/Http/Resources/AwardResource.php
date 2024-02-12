<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AwardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image ? Storage::url($this->image) : null,
            'people_count' => $this->people_count,

            'images' => ImageResource::collection($this->whenLoaded('images')),

            'date' => $this->whenPivotLoaded('award_person', fn () => $this->pivot->date),
            'additional_info' => $this->whenPivotLoaded(
                'award_person',
                fn () => $this->pivot->additional_info
            ),
        ];
    }
}
