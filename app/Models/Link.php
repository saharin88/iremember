<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
    ];

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class);
    }
}
