<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Actions\Animal\CalculateAdg;

class WeightLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'weigh_date' => 'date',
        'weight_kg' => 'float',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
