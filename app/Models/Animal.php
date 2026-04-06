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
        'entry_date' => 'date',
        'is_active' => 'boolean',
        'current_hpp' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'accumulated_feed_cost' => 'decimal:2',
        'accumulated_medicine_cost' => 'decimal:2',
        'daily_adg' => 'float',
    ];

    public function getFullBreedAttribute()
    {
        $gen = $this->generation ? $this->generation . ' ' : '';
        $breed = $this->breed->name ?? '-';
        return $gen . $breed;
    }

    public function getAgeStringAttribute()
    {
        if (!$this->birth_date) return '-';
        $diff = \Carbon\Carbon::now()->diff($this->birth_date);
        
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' thn';
        if ($diff->m > 0) $parts[] = $diff->m . ' bln';
        if ($diff->d > 0 && empty($parts)) $parts[] = $diff->d . ' hr';
        
        return implode(' ', $parts) ?: 'Baru Lahir';
    }

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

    public function latestWeightLog()
    {
        return $this->hasOne(WeightLog::class)->latestOfMany('weigh_date');
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

    public function invoiceItem()
    {
        return $this->hasOne(InvoiceItem::class, 'related_animal_id');
    }

    public function offspring(): HasMany
    {
        return $this->hasMany(Animal::class, 'dam_id');
    }

    public function ownershipLogs(): HasMany
    {
        return $this->hasMany(AnimalOwnershipLog::class);
    }

    public function earTagLogs(): HasMany
    {
        return $this->hasMany(AnimalEarTagLog::class);
    }

    public function breedingEvents(): HasMany
    {
        return $this->hasMany(BreedingEvent::class, $this->gender === 'JANTAN' ? 'sire_id' : 'dam_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(AnimalTask::class);
    }
}
