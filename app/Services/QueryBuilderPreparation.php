<?php

namespace App\Services;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Requests\Api\V1\ApiRequest;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class QueryBuilderPreparation implements QueryBuilderPreparationInterface
{
    public function prepareQueryBuilder(ApiRequest|FormRequest $request, ?string $model = null): Builder
    {
        $model ??= Person::class;
        $query = $model::query();

        if ($request->has('load')) {
            $query->with($request->input('load'));
        }

        if ($request->has('filter')) {
            foreach ($request->input('filter', []) as $key => $value) {
                match ($key) {
                    'name' => $query->where('name', 'like', "%$value%"),
                    'date' => $query->whereDate('date', $value),
                    'start' => $query->whereDate('start', $value),
                    'end' => $query->whereDate('end', $value),
                    'full_name' => $query->where(function ($query) use ($value) {
                        if (str_word_count($value) === 1) {
                            $query->where('first_name', 'like', "$value%")
                                ->orWhere('last_name', 'like', "$value%")
                                ->orWhere('middle_name', 'like', "$value%");
                        } else {
                            $query->where('full_name', 'LIKE', "%$value%");
                        }
                    }),
                    'birth', 'death', 'burial', 'wound' => $query->whereDate($key, $value),
                    'birth_location_id', 'death_location_id', 'burial_location_id', 'wound_location_id', 'unit_id' => is_array($value)
                        ? $query->whereIn($key, $value)
                        : $query->where($key, $value),
                    'death_year', 'birth_year', 'burial_year', 'wound_year' => $query->where(function ($query) use ($key, $value) {
                        $column = str_replace('_year', '', $key);
                        $query->whereYear($column, $value);
                    }),
                    'birth_day', 'death_day', 'burial_day', 'wound_day' => $query->where(function ($query) use ($key, $value) {
                        $column = str_replace('_day', '', $key);
                        $month = substr($value, 0,2);
                        $day = substr($value, 2);
                        $query->whereMonth($column, $month)
                            ->whereDay($column, $day);
                    }),
                    'age' => $query->where(function ($query) use ($value) {
                        if (DB::getDriverName() === 'sqlite') {
                            $query->whereRaw('CAST(strftime("%Y", death) - strftime("%Y", birth) - (strftime("%m-%d", death) < strftime("%m-%d", birth)) AS INT) = ?', [$value]);
                        } else {
                            $query->whereRaw('TIMESTAMPDIFF(YEAR, birth, death) = ?', [$value]);
                        }
                    }),
                    default => $query->where($key, $value),
                };
            }
        }

        if ($request->has('sort')) {
            $direction = $request->input('order', 'asc');
            match ($request->input('sort')) {
                'full_name' => $query
                    ->orderBy('first_name', $direction)
                    ->orderBy('last_name', $direction)
                    ->orderBy('middle_name', $direction),
                default => $query->orderBy($request->input('sort'), $direction),
            };
        }

        return $query;
    }
}
