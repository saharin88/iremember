<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

abstract class ApiRequest extends FormRequest
{
    protected int $maxLimit = 500;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $data = [
            'limit' => 'sometimes|integer|min:1|max:'.$this->maxLimit,
            'page' => 'sometimes|integer|min:1',
        ];

        if ($allowedRelations = $this->getAllowedRelations()) {
            $data['load'] = 'sometimes|array';
            $data['load.*'] = 'in:'.implode(',', $allowedRelations);
        } else {
            $data['load'] = 'prohibited';
        }

        if ($allowedSortColumns = $this->getAllowedSortingColumns()) {
            $data['sort'] = 'required_with:order|string|in:'.implode(',', $allowedSortColumns);
            $data['order'] = 'sometimes|string|in:asc,desc';
        } else {
            $data['sort'] = $data['order'] = $data['limit'] = $data['page'] = 'prohibited';
        }

        if ($filterFields = $this->getFilters()) {
            $filterKeys = array_keys($filterFields);
            $data['filter'] = 'sometimes|array:'.implode(',', $filterKeys);
            $filterFields = array_combine(
                array_map(fn ($key) => 'filter.'.$key, $filterKeys),
                $filterFields
            );
            $data = array_merge($data, $filterFields);
        } else {
            $data['filter'] = 'prohibited';
        }

        return $data;
    }

    public function messages(): array
    {
        $allowedRelations = implode(', ', $this->getAllowedRelations());
        $allowedSortColumns = implode(', ', $this->getAllowedSortingColumns());
        $allowedFilters = implode(', ', array_keys($this->getFilters()));

        return [
            'load.*.in' => __('The load[] field must be one of: :relations.', ['relations' => $allowedRelations]),
            'sort.in' => __('The sort field must be one of: :sortFields.', ['sortFields' => $allowedSortColumns]),
            'filter.array' => __('The filter field must be an array with keys: :allowedFilters.',
                ['allowedFilters' => $allowedFilters]),

        ];
    }

    /**
     * Get the relations that are allowed to be loaded.
     *
     * @return array<string>
     */
    abstract public function getAllowedRelations(): array;

    /**
     * Get the fields that are allowed to be sorted.
     *
     * @return array<string>
     */
    abstract public function getAllowedSortingColumns(): array;

    /**
     * Get the fields that are allowed to be filtered.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    abstract public function getFilters(): array;
}
