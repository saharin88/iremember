<?php

namespace App\Models;

use App\Enums\Citizenship;
use App\Enums\Sex;
use App\Enums\State;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;

class Person extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable, HasFactory, Sluggable, SoftDeletes;

    protected $auditExclude = [
        'photo',
    ];

    protected $fillable = [
        'state',
        'first_name',
        'last_name',
        'middle_name',
        'call_sign',
        'slug',
        'birth',
        'death',
        'burial',
        'wound',
        'birth_location_id',
        'death_location_id',
        'burial_location_id',
        'wound_location_id',
        'death_details',
        'obituary',
        'citizenship',
        'unit_id',
        'rank_id',
        'military_position_id',
        'photo',
        'sex',
        'civil',
    ];

    protected $casts = [
        'birth' => 'date',
        'death' => 'date',
        'burial' => 'date',
        'wound' => 'date',
        'sex' => Sex::class,
        'citizenship' => Citizenship::class,
        'civil' => 'boolean',
        'state' => State::class,
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['first_name', 'last_name', 'middle_name'],
            ],
        ];
    }

    public function awards(): BelongsToMany
    {
        return $this->belongsToMany(Award::class)->withPivot(['date', 'additional_info']);
    }

    public function battles(): BelongsToMany
    {
        return $this->belongsToMany(Battle::class);
    }

    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class);
    }

    public function memorials(): BelongsToMany
    {
        return $this->belongsToMany(Memorial::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class)
            ->withPivot(['start', 'end', 'rank_id', 'military_position_id'])
            ->using(PersonUnit::class);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function militaryPosition(): BelongsTo
    {
        return $this->belongsTo(MilitaryPosition::class);
    }

    public function birthLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function deathLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function burialLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function woundLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
