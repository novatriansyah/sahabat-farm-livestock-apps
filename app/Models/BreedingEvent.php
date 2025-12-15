<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreedingEvent extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'mating_date' => 'date',
    ];

    public function dam(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'dam_id');
    }

    public function sire(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'sire_id');
    }
}
