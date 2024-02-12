<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MemorialRequest;
use App\Http\Requests\Api\V1\MemorialsRequest;
use App\Http\Resources\MemorialResource;
use App\Models\Memorial;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group(name: 'Memorials')]
class MemorialController extends Controller
{
    #[Endpoint(title: 'List memorials', description: 'Get a list of memorials')]
    #[QueryParam(name: 'limit', type: 'integer', description: 'Number of items to return per page', required: false, example: 100)]
    #[QueryParam(name: 'page', type: 'integer', description: 'Page number', required: false, example: 1)]
    #[QueryParam(name: 'sort', type: 'string', description: 'Sort by column', required: false, example: 'name', enum: ['name', 'date', 'people_count'])]
    #[QueryParam(name: 'order', type: 'string', description: 'Sort order', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['location'], enum: ['location', 'people', 'images'])]
    #[QueryParam(name: 'filter[name]', type: 'string', description: 'Filter by name', required: false)]
    #[QueryParam(name: 'filter[date]', type: 'string', description: 'Filter by date', required: false)]
    #[QueryParam(name: 'filter[location_id]', type: 'integer', description: 'Filter by location id', required: false)]
    #[ResponseFromApiResource(MemorialResource::class, model: Memorial::class, collection: true, with: ['location', 'people', 'images'], paginate: 10)]
    public function index(MemorialsRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Memorial::class);

        return MemorialResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    #[Endpoint(title: 'Get memorial', description: 'Get a single memorial')]
    #[UrlParam(name: 'memorial', type: 'integer', description: 'Memorial ID', required: true, example: 1)]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['location'], enum: ['location', 'people', 'images'])]
    #[ResponseFromApiResource(MemorialResource::class, model: Memorial::class, with: ['location', 'people', 'images'])]
    public function show(MemorialRequest $request, Memorial $memorial)
    {
        if ($request->has('load')) {
            $memorial->load($request->get('load'));
        }

        return MemorialResource::make($memorial);
    }
}
