<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;

class Memorial extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable, HasFactory, Sluggable, SoftDeletes;

    protected $auditExclude = [
        'image',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'location_id',
        'image',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
