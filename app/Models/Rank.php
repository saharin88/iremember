<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'military_branch_id',
        'image',
    ];

    protected $withCount = [
        'people',
    ];

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function militaryBranch(): BelongsTo
    {
        return $this->belongsTo(MilitaryBranch::class);
    }
}
