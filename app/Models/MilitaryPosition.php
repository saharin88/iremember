<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MilitaryPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $withCount = [
        'people',
    ];

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
