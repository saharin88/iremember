<?php

namespace App\Http\Requests\Api\V1;

class MilitaryBranchesRequest extends ApiRequest
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
        return ['parent', 'ancestors', 'children', 'descendants', 'ranks', 'units'];
    }

    public function getAllowedSortingColumns(): array
    {
        return ['name'];
    }

    public function getFilters(): array
    {
        return [
            'name' => 'sometimes|min:3|max:255',
            'parent_id' => 'sometimes|integer|exists:military_branches,id',
        ];
    }
}
