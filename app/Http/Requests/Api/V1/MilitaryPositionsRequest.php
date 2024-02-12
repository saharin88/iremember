<?php

namespace App\Http\Requests\Api\V1;

class MilitaryPositionsRequest extends ApiRequest
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
        return [];
    }

    public function getAllowedSortingColumns(): array
    {
        return ['name', 'people_count'];
    }

    public function getFilters(): array
    {
        return [
            'name' => 'sometimes|min:3|max:255',
        ];
    }
}
