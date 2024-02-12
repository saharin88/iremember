<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RankRequest;
use App\Http\Requests\Api\V1\RanksRequest;
use App\Http\Resources\RankResource;
use App\Models\Rank;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group(name: 'Ranks')]
class RankController extends Controller
{
    #[Endpoint(title: 'List ranks', description: 'Get a list of ranks')]
    #[QueryParam(name: 'limit', type: 'integer', description: 'Number of items to return per page', required: false, example: 100)]
    #[QueryParam(name: 'page', type: 'integer', description: 'Page number', required: false, example: 1)]
    #[QueryParam(name: 'sort', type: 'string', description: 'Sort by column', required: false, example: 'name', enum: ['name', 'people_count'])]
    #[QueryParam(name: 'order', type: 'string', description: 'Sort order', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['militaryBranch'], enum: ['militaryBranch'])]
    #[QueryParam(name: 'filter[name]', type: 'string', description: 'Filter by name')]
    #[QueryParam(name: 'filter[military_branch_id]', type: 'integer', description: 'Filter by military branch id')]
    #[ResponseFromApiResource(RankResource::class, model: Rank::class, collection: true, with: ['militaryBranch'], paginate: 10)]
    public function index(RanksRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Rank::class);

        return RankResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    #[Endpoint(title: 'Get rank', description: 'Get a single rank')]
    #[UrlParam(name: 'rank', type: 'integer', description: 'Rank ID', required: true, example: 1)]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', example: ['militaryBranch'], enum: ['militaryBranch'])]
    #[ResponseFromApiResource(RankResource::class, model: Rank::class, with: ['militaryBranch'])]
    public function show(RankRequest $request, Rank $rank)
    {
        if ($request->has('load')) {
            $rank->load($request->get('load'));
        }

        return RankResource::make($rank);
    }
}
