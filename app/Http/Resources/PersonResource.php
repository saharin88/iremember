<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PersonResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'full_name' => $this->full_name,
            'call_sign' => $this->call_sign,
            'slug' => $this->slug,
            'birth' => $this->birth?->format('Y-m-d'),
            'death' => $this->death?->format('Y-m-d'),
            'burial' => $this->burial?->format('Y-m-d'),
            'wound' => $this->wound?->format('Y-m-d'),
            'birth_location_id' => $this->birth_location_id,
            'death_location_id' => $this->death_location_id,
            'burial_location_id' => $this->burial_location_id,
            'wound_location_id' => $this->wound_location_id,
            'death_details' => $this->death_details,
            'obituary' => $this->obituary,
            'citizenship' => $this->citizenship,
            'unit_id' => $this->unit_id,
            'rank_id' => $this->rank_id,
            'military_position_id' => $this->military_position_id,
            'photo' => $this->photo ? Storage::url($this->photo) : null,
            'sex' => $this->sex,
            'civil' => $this->civil,

            'birthLocation' => LocationResource::make($this->whenLoaded('birthLocation')),
            'deathLocation' => LocationResource::make($this->whenLoaded('deathLocation')),
            'burialLocation' => LocationResource::make($this->whenLoaded('burialLocation')),
            'woundLocation' => LocationResource::make($this->whenLoaded('woundLocation')),

            'unit' => UnitResource::make($this->whenLoaded('unit')),
            'rank' => RankResource::make($this->whenLoaded('rank')),
            'militaryPosition' => MilitaryPositionResource::make($this->whenLoaded('militaryPosition')),

            'awards' => AwardResource::collection($this->whenLoaded('awards')),
            'battles' => BattleResource::collection($this->whenLoaded('battles')),
            'photos' => ImageResource::collection($this->whenLoaded('photos')),
            'links' => LinkResource::collection($this->whenLoaded('links')),
            'memorials' => MemorialResource::collection($this->whenLoaded('memorials')),
            'units' => UnitResource::collection($this->whenLoaded('units')),
        ];

        return $data;
    }
}
