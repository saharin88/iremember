<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PersonUnit extends Pivot
{
    protected $table = 'person_unit';

    protected $fillable = [
        'person_id',
        'unit_id',
        'rank_id',
        'military_position_id',
        'start',
        'end',
    ];

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function militaryPosition(): BelongsTo
    {
        return $this->belongsTo(MilitaryPosition::class);
    }
}
