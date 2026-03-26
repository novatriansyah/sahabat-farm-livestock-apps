<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardSetting extends Model
{
    protected $fillable = [
        'user_id',
        'component_name',
        'is_visible',
        'order',
    ];
}
