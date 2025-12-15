<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryUsageLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'usage_date' => 'date',
        'qty_used' => 'float',
        'qty_wasted' => 'float',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
