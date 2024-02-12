<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;

interface QueryBuilderPreparationInterface
{
    public function prepareQueryBuilder(FormRequest $request): Builder;
}
