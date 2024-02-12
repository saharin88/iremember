<?php

namespace App\Http\Requests\Api\V1;

class LocationsRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function getAllowedRelations(): array
    {
        return ['parent', 'ancestors', 'children', 'descendants', 'images'];
    }

    public function getAllowedSortingColumns(): array
    {
        return ['name', 'people_birth_count', 'people_death_count', 'people_burial_count', 'people_wound_count'];
    }

    public function getFilters(): array
    {
        return [
            'name' => 'sometimes|min:3|max:255',
            'lat' => 'sometimes|between:-90,90',
            'lng' => 'sometimes|between:-180,180',
            'koatuu' => 'sometimes|digits:10',
            'katottg' => 'sometimes|size:19',
            'parent_id' => 'sometimes|integer|exists:locations,id',
        ];
    }
}
