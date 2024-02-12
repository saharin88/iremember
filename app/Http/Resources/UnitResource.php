<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UnitResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'military_branch_id' => $this->military_branch_id,
            'full_name' => $this->full_name,
            'description' => $this->description,
            'image' => $this->image ? Storage::url($this->image) : null,
            'people_count' => $this->people_count,

            'parent' => self::make($this->whenLoaded('parent')),
            'ancestors' => self::make($this->whenLoaded('ancestors')),
            'children' => self::collection($this->whenLoaded('children')),
            'descendants' => self::collection($this->whenLoaded('descendants')),

            'images' => ImageResource::collection($this->whenLoaded('images')),
            'militaryBranch' => MilitaryBranchResource::make($this->whenLoaded('militaryBranch')),

            'start' => $this->whenPivotLoaded('person_unit', fn () => $this->pivot->start),
            'end' => $this->whenPivotLoaded('person_unit', fn () => $this->pivot->end),
            'rank' => $this->whenPivotLoaded('person_unit', fn () => $this->pivot->rank),
            'militaryPosition' => $this->whenPivotLoaded('person_unit', fn () => $this->pivot->militaryPosition),
        ];

        return $data;
    }
}
