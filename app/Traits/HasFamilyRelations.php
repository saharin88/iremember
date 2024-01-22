<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasFamilyRelations
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, self::PARENT_ID);
    }

    public function ancestors(): BelongsTo
    {
        return $this->parent()->with('ancestors');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, self::PARENT_ID);
    }

    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }
}
