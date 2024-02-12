<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MilitaryPositionsRequest;
use App\Http\Resources\MilitaryPositionResource;
use App\Models\MilitaryPosition;
use App\Services\QueryBuilderPreparation;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group(name: 'Military positions')]
class MilitaryPositionController extends Controller
{
    #[Endpoint(title: 'List military positions', description: 'Get a list of military positions')]
    #[QueryParam(name: 'limit', type: 'integer', description: 'Number of items to return per page', required: false, example: 100)]
    #[QueryParam(name: 'page', type: 'integer', description: 'Page number', required: false, example: 1)]
    #[QueryParam(name: 'sort', type: 'string', description: 'Sort by column', required: false, example: 'name', enum: ['name', 'people_count'])]
    #[QueryParam(name: 'order', type: 'string', description: 'Sort order', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam(name: 'filter[name]', type: 'string', description: 'Filter by name', required: false)]
    #[ResponseFromApiResource(MilitaryPositionResource::class, model: MilitaryPosition::class, collection: true, paginate: 10)]
    public function index(MilitaryPositionsRequest $request, QueryBuilderPreparation $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, MilitaryPosition::class);

        return MilitaryPositionResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    #[Endpoint(title: 'Show military position', description: 'Get a single military position')]
    #[UrlParam(name: 'military_position', type: 'integer', description: 'Military position ID', required: true, example: 1)]
    #[ResponseFromApiResource(MilitaryPositionResource::class, model: MilitaryPosition::class)]
    public function show(MilitaryPosition $militaryPosition)
    {
        return MilitaryPositionResource::make($militaryPosition);
    }
}
