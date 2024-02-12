<?php

namespace App\Http\Requests\Api\V1;

class RankRequest extends ApiRequest
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
        return [];
    }

    public function getFilters(): array
    {
        return [];
    }
}
