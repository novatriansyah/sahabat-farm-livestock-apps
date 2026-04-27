<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDisease extends Model
{
    protected $guarded = [];

    public function recommendedTreatments()
    {
        return $this->belongsToMany(InventoryItem::class, 'disease_treatments')
                    ->withPivot('custom_dosage')
                    ->withTimestamps();
    }
}
