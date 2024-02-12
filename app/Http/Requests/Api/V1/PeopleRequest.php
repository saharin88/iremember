<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Citizenship;
use App\Enums\Sex;
use App\Traits\PersonTrait;
use Illuminate\Validation\Rule;

class PeopleRequest extends ApiRequest
{
    use PersonTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function getAllowedRelations(): array
    {
        return self::RELATIONS;
    }

    public function getAllowedSortingColumns(): array
    {
        return ['full_name', 'birth', 'death'];
    }

    public function getFilters(): array
    {
        return [
            'full_name' => 'string|min:3|max:255',
            'sex' => [Rule::enum(Sex::class)],
            'citizenship' => [Rule::enum(Citizenship::class)],
            'civil' => 'in:0,1',
            'birth' => 'date:Y-m-d',
            'death' => 'date:Y-m-d',
            'burial' => 'date:Y-m-d',
            'wound' => 'date:Y-m-d',
            'birth_location_id' => 'integer|exists:locations,id',
            'death_location_id' => 'integer|exists:locations,id',
            'burial_location_id' => 'integer|exists:locations,id',
            'wound_location_id' => 'integer|exists:locations,id',
            'unit_id' => 'integer|exists:units,id',
            'rank_id' => 'integer|exists:ranks,id',
            'military_position_id' => 'integer|exists:military_positions,id',
            'death_year' => 'integer|gte:2014|lte:'.date('Y'),
            'birth_year' => 'integer|lte:'.date('Y'),
            'burial_year' => 'integer|gte:2014|lte:'.date('Y'),
            'wound_year' => 'integer|gte:2014|lte:'.date('Y'),
            'death_day' => 'numeric|between:0101,1231',
            'birth_day' => 'numeric|between:0101,1231',
            'burial_day' => 'numeric|between:0101,1231',
            'wound_day' => 'numeric|between:0101,1231',
            'age' => 'integer|between:0,150',
        ];
    }

    public function attributes(): array
    {
        return [
            'filter.full_name' => 'filter[full_name]',
            'filter.sex' => 'filter[sex]',
            'filter.citizenship' => 'filter[citizenship]',
            'filter.civil' => 'filter[civil]',
            'filter.birth' => 'filter[birth]',
            'filter.death' => 'filter[death]',
            'filter.burial' => 'filter[burial]',
            'filter.wound' => 'filter[wound]',
            'filter.birth_location_id' => 'filter[birth_location_id]',
            'filter.death_location_id' => 'filter[death_location_id]',
            'filter.burial_location_id' => 'filter[burial_location_id]',
            'filter.wound_location_id' => 'filter[wound_location_id]',
            'filter.unit_id' => 'filter[unit_id]',
            'filter.rank_id' => 'filter[rank_id]',
            'filter.military_position_id' => 'filter[military_position_id]',
            'filter.age' => 'filter[age]',
        ];
    }

}
