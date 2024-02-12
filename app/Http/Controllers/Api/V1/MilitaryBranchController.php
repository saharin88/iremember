<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MilitaryBranchesRequest;
use App\Http\Requests\Api\V1\MilitaryBranchRequest;
use App\Http\Resources\MilitaryBranchResource;
use App\Models\MilitaryBranch;

class MilitaryBranchController extends Controller
{
    public function index(MilitaryBranchesRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, MilitaryBranch::class);

        return MilitaryBranchResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    public function show(MilitaryBranchRequest $request, MilitaryBranch $militaryBranch)
    {
        if ($request->has('load')) {
            $militaryBranch->load($request->get('load'));
        }

        return MilitaryBranchResource::make($militaryBranch);
    }
}
