<?php

namespace App\Models;

use App\Contracts\FamilyRelationsInterface;
use App\Traits\HasFamilyRelations;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;

class Unit extends Model implements \OwenIt\Auditing\Contracts\Auditable, FamilyRelationsInterface
{
    use Auditable, HasFactory, HasFamilyRelations, Sluggable, SoftDeletes;

    protected $auditExclude = [
        'image',
    ];

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'military_branch_id',
        'full_name',
        'description',
        'image',
    ];

    protected $withCount = [
        'people',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function peoplePivot(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'person_unit');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function militaryBranch(): BelongsTo
    {
        return $this->belongsTo(MilitaryBranch::class);
    }
}
