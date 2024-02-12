<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\PersonTrait;

class PersonRequest extends ApiRequest
{
    use PersonTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_intersect_key(parent::rules(), array_flip(['load', 'load.*']));
    }

    public function getAllowedRelations(): array
    {
        return self::RELATIONS;
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
