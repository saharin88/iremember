<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LocationRequest;
use App\Http\Requests\Api\V1\LocationsRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group(name: 'Locations')]
class LocationController extends Controller
{
    #[Endpoint(title: 'List locations', description: 'Get a list of locations')]
    #[QueryParam(name: 'limit', type: 'integer', description: 'Number of items to return per page', required: false, example: 100)]
    #[QueryParam(name: 'page', type: 'integer', description: 'Page number', required: false, example: 1)]
    #[QueryParam(name: 'sort', type: 'string', description: 'Sort by column', required: false, example: 'name', enum: ['name', 'people_birth_count', 'people_death_count', 'people_burial_count', 'people_wound_count'])]
    #[QueryParam(name: 'order', type: 'string', description: 'Sort order', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['images'], enum: ['parent', 'ancestors', 'children', 'descendants', 'images'])]
    #[QueryParam(name: 'filter[name]', type: 'string', description: 'Filter by name', required: false)]
    #[QueryParam(name: 'filter[parent_id]', type: 'integer', description: 'Filter by parent id', required: false)]
    #[QueryParam(name: 'filter[lat]', type: 'number', description: 'Filter by latitude', required: false)]
    #[QueryParam(name: 'filter[lng]', type: 'number', description: 'Filter by longitude', required: false)]
    #[QueryParam(name: 'filter[koatuu]', type: 'string', description: 'Filter by KOATUU', required: false)]
    #[QueryParam(name: 'filter[katottg]', type: 'string', description: 'Filter by KATOTTG', required: false)]
    #[ResponseFromApiResource(LocationResource::class, model: Location::class, collection: true, with: ['parent', 'ancestors', 'children', 'descendants', 'images'], paginate: 10)]
    public function index(LocationsRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Location::class);

        return LocationResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    #[Endpoint(title: 'Get location', description: 'Get a single location')]
    #[UrlParam(name: 'location', type: 'integer', description: 'Location ID', required: true, example: 1)]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['images'], enum: ['parent', 'ancestors', 'children', 'descendants', 'images'])]
    #[ResponseFromApiResource(LocationResource::class, model: Location::class, with: ['parent', 'ancestors', 'children', 'descendants', 'images'])]
    public function show(LocationRequest $request, Location $location)
    {
        if ($request->has('load')) {
            $location->load($request->get('load'));
        }

        return LocationResource::make($location);
    }
}
