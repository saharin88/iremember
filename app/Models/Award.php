<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;

class Award extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable, HasFactory, Sluggable, SoftDeletes;

    protected $auditExclude = [
        'image',
    ];

    protected $fillable = [
        'name',
        'slug',
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

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)->withPivot('date', 'additional_info');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
