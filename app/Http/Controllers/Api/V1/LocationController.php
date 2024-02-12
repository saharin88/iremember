<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LocationRequest;
use App\Http\Requests\Api\V1\LocationsRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;

class LocationController extends Controller
{
    public function index(LocationsRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Location::class);

        return LocationResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    public function show(LocationRequest $request, Location $location)
    {
        if ($request->has('load')) {
            $location->load($request->get('load'));
        }

        return LocationResource::make($location);
    }
}
