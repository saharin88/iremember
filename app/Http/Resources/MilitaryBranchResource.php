<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MilitaryBranchResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'image' => $this->image ? Storage::url($this->image) : null,

            'parent' => self::make($this->whenLoaded('parent')),
            'ancestors' => self::make($this->whenLoaded('ancestors')),
            'children' => self::collection($this->whenLoaded('children')),
            'descendants' => self::collection($this->whenLoaded('descendants')),

            'ranks' => RankResource::collection($this->whenLoaded('ranks')),
            'units' => UnitResource::collection($this->whenLoaded('units')),
        ];
    }
}
