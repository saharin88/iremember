<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MilitaryBranchesRequest;
use App\Http\Requests\Api\V1\MilitaryBranchRequest;
use App\Http\Resources\MilitaryBranchResource;
use App\Models\MilitaryBranch;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group(name: 'Military branches')]
class MilitaryBranchController extends Controller
{
    #[Endpoint(title: 'List military branches', description: 'Get a list of military branches')]
    #[QueryParam(name: 'limit', type: 'integer', description: 'Number of items to return per page', required: false, example: 100)]
    #[QueryParam(name: 'page', type: 'integer', description: 'Page number', required: false, example: 1)]
    #[QueryParam(name: 'sort', type: 'string', description: 'Sort by column', required: false, example: 'name', enum: ['name'])]
    #[QueryParam(name: 'order', type: 'string', description: 'Sort order', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['units'], enum: ['parent', 'ancestors', 'children', 'descendants', 'ranks', 'units'])]
    #[QueryParam(name: 'filter[name]', type: 'string', description: 'Filter by name', required: false)]
    #[QueryParam(name: 'filter[parent_id]', type: 'integer', description: 'Filter by parent id', required: false)]
    #[ResponseFromApiResource(MilitaryBranchResource::class, model: MilitaryBranch::class, collection: true, with: ['parent', 'ancestors', 'children', 'descendants', 'ranks', 'units'], paginate: 10)]
    public function index(MilitaryBranchesRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, MilitaryBranch::class);

        return MilitaryBranchResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    #[Endpoint(title: 'Get military branch', description: 'Get a single military branch')]
    #[UrlParam(name: 'military_branch', type: 'integer', description: 'Military branch ID', required: true, example: 1)]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['units'], enum: ['parent', 'ancestors', 'children', 'descendants', 'ranks', 'units'])]
    #[ResponseFromApiResource(MilitaryBranchResource::class, model: MilitaryBranch::class, with: ['parent', 'ancestors', 'children', 'descendants', 'ranks', 'units'])]
    public function show(MilitaryBranchRequest $request, MilitaryBranch $militaryBranch)
    {
        if ($request->has('load')) {
            $militaryBranch->load($request->get('load'));
        }

        return MilitaryBranchResource::make($militaryBranch);
    }
}
