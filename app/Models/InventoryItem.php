<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'current_stock' => 'float',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(InventoryPurchase::class, 'item_id');
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(InventoryUsageLog::class, 'item_id');
    }
}
