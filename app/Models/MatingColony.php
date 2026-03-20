<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatingColony extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function sire(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'sire_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(MasterLocation::class, 'location_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(MatingColonyMember::class);
    }
}
