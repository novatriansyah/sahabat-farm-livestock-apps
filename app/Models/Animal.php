<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Animal extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'current_hpp' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'accumulated_feed_cost' => 'decimal:2',
        'accumulated_medicine_cost' => 'decimal:2',
        'daily_adg' => 'float',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(MasterPartner::class, 'partner_id');
    }

    public function sire(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'sire_id');
    }

    public function dam(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'dam_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MasterCategory::class);
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(MasterBreed::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(MasterLocation::class, 'current_location_id');
    }

    public function physStatus(): BelongsTo
    {
        return $this->belongsTo(MasterPhysStatus::class, 'current_phys_status_id');
    }

    public function weightLogs(): HasMany
    {
        return $this->hasMany(WeightLog::class);
    }

    public function treatmentLogs(): HasMany
    {
        return $this->hasMany(TreatmentLog::class);
    }

    public function exitLogs(): HasMany
    {
        return $this->hasMany(ExitLog::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AnimalPhoto::class);
    }
}
