<?php

namespace App\Models;

use App\Contracts\FamilyRelationsInterface;
use App\Traits\HasFamilyRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MilitaryBranch extends Model implements FamilyRelationsInterface
{
    use HasFactory, HasFamilyRelations;

    protected $fillable = [
        'name',
        'parent_id',
        'image',
    ];

    public function ranks(): HasMany
    {
        return $this->hasMany(Rank::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
