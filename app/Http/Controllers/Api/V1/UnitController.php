<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UnitRequest;
use App\Http\Requests\Api\V1\UnitsRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group(name: 'Units')]
class UnitController extends Controller
{
    #[Endpoint(title: 'List units', description: 'Get a list of units')]
    #[QueryParam(name: 'limit', type: 'integer', description: 'Number of items to return per page', required: false, example: 100)]
    #[QueryParam(name: 'page', type: 'integer', description: 'Page number', required: false, example: 1)]
    #[QueryParam(name: 'sort', type: 'string', description: 'Sort by column', required: false, example: 'name', enum: ['name', 'people_count'])]
    #[QueryParam(name: 'order', type: 'string', description: 'Sort order', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['images'], enum: ['parent', 'ancestors', 'children', 'descendants', 'militaryBranch', 'images'])]
    #[QueryParam(name: 'filter[name]', type: 'string', description: 'Filter by name', required: false)]
    #[QueryParam(name: 'filter[parent_id]', type: 'integer', description: 'Filter by parent id', required: false)]
    #[QueryParam(name: 'filter[military_branch_id]', type: 'integer', description: 'Filter by military branch id', required: false)]
    #[ResponseFromApiResource(UnitResource::class, model: Unit::class, collection: true, with: ['parent', 'ancestors', 'children', 'descendants', 'militaryBranch', 'images'], paginate: 10)]
    public function index(UnitsRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Unit::class);

        return UnitResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    #[Endpoint(title: 'Get unit', description: 'Get a single unit')]
    #[UrlParam(name: 'unit', type: 'integer', description: 'Unit ID', required: true, example: 1)]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', example: ['images'], enum: ['parent', 'ancestors', 'children', 'descendants', 'militaryBranch', 'images'])]
    #[ResponseFromApiResource(UnitResource::class, model: Unit::class, with: ['parent', 'ancestors', 'children', 'descendants', 'militaryBranch', 'images'])]
    public function show(UnitRequest $request, Unit $unit)
    {
        if ($request->has('load')) {
            $unit->load($request->get('load'));
        }

        return new UnitResource($unit);
    }
}
