<?php

namespace App\Http\Requests\Api\V1;

class RanksRequest extends ApiRequest
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
        return ['militaryBranch'];
    }

    public function getAllowedSortingColumns(): array
    {
        return ['name', 'people_count'];
    }

    public function getFilters(): array
    {
        return [
            'name' => 'sometimes|min:3|max:255',
            'military_branch_id' => 'sometimes|integer|exists:military_branches,id',
        ];
    }
}
