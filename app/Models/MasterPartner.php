<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPartner extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact_info'];

    public function animals()
    {
        return $this->hasMany(Animal::class, 'partner_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'partner_id');
    }
}
