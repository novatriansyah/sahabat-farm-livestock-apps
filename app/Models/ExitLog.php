<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExitLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'exit_date' => 'date',
        'price' => 'decimal:2',
        'final_hpp' => 'decimal:2',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
