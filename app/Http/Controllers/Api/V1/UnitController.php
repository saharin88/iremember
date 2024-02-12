<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UnitRequest;
use App\Http\Requests\Api\V1\UnitsRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;

class UnitController extends Controller
{
    public function index(UnitsRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Unit::class);

        return UnitResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    public function show(UnitRequest $request, Unit $unit)
    {
        if ($request->has('load')) {
            $unit->load($request->get('load'));
        }

        return new UnitResource($unit);
    }
}
