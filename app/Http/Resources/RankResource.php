<?php

namespace App\Http\Resources;

use App\Http\Requests\Api\V1\RanksRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(RanksRequest|Request $request): array
    {

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'military_branch_id' => $this->military_branch_id,
            'image' => $this->image ? Storage::url($this->image) : null,
            'people_count' => $this->people_count,

            'militaryBranch' => MilitaryBranchResource::make($this->whenLoaded('militaryBranch')),
        ];

        return $data;
    }
}
