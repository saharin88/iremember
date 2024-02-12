<?php

namespace App\Http\Requests\Api\V1;

class MemorialsRequest extends ApiRequest
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
        return ['location', 'people', 'images'];
    }

    public function getAllowedSortingColumns(): array
    {
        return ['name', 'date', 'people_count'];
    }

    public function getFilters(): array
    {
        return [
            'name' => 'sometimes|min:3|max:255',
            'location_id' => 'sometimes|integer|exists:locations,id',
            'date' => 'sometimes|date:Y-m-d',
        ];
    }
}
