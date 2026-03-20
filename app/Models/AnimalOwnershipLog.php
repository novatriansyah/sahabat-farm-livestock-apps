<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalOwnershipLog extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $casts = [
        'changed_at' => 'date',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function oldPartner()
    {
        return $this->belongsTo(MasterPartner::class, 'old_partner_id');
    }

    public function newPartner()
    {
        return $this->belongsTo(MasterPartner::class, 'new_partner_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
