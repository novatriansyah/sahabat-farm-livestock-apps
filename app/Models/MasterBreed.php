<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterBreed extends Model
{
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MasterCategory::class);
    }

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class, 'breed_id');
    }
}
