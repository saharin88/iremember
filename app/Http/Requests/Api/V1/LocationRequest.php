<?php

namespace App\Http\Requests\Api\V1;

class LocationRequest extends ApiRequest
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
        return [];
    }

    public function getFilters(): array
    {
        return [];
    }
}
