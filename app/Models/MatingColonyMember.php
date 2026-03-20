<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatingColonyMember extends Model
{
    protected $guarded = [];

    protected $casts = [
        'joined_date' => 'date',
        'left_date' => 'date',
    ];

    public function colony(): BelongsTo
    {
        return $this->belongsTo(MatingColony::class, 'mating_colony_id');
    }

    public function dam(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'dam_id');
    }
}
