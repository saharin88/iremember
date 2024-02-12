<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface FamilyRelationsInterface
{
    const string PARENT_ID = 'parent_id';

    public function parent(): BelongsTo;

    public function ancestors(): BelongsTo;

    public function children(): HasMany;

    public function descendants(): HasMany;
}
