<?php

namespace App\Models;

use App\Contracts\FamilyRelationsInterface;
use App\Traits\HasFamilyRelations;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;

class Location extends Model implements \OwenIt\Auditing\Contracts\Auditable, FamilyRelationsInterface
{
    use Auditable, HasFactory, HasFamilyRelations, Sluggable, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'lat',
        'lng',
        'koatuu',
        'katottg',
        'parent_id',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'is_administrative_division' => 'boolean',
    ];

    protected $withCount = [
        'peopleBirth',
        'peopleDeath',
        'peopleBurial',
        'peopleWound',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function isAdministrativeDivision(): Attribute
    {
        return new Attribute(
            get: fn () => ! empty($this->koatuu),
        );
    }

    public function peopleBirth(): HasMany
    {
        return $this->hasMany(Person::class, 'birth_location_id');
    }

    public function peopleDeath(): HasMany
    {
        return $this->hasMany(Person::class, 'death_location_id');
    }

    public function peopleBurial(): HasMany
    {
        return $this->hasMany(Person::class, 'burial_location_id');
    }

    public function peopleWound(): HasMany
    {
        return $this->hasMany(Person::class, 'wound_location_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
