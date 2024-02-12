<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MilitaryPositionsRequest;
use App\Http\Resources\MilitaryPositionResource;
use App\Models\MilitaryPosition;
use App\Services\QueryBuilderPreparation;

class MilitaryPositionController extends Controller
{
    public function index(MilitaryPositionsRequest $request, QueryBuilderPreparation $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, MilitaryPosition::class);

        return MilitaryPositionResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    public function show(MilitaryPosition $militaryPosition)
    {
        return MilitaryPositionResource::make($militaryPosition);
    }
}
