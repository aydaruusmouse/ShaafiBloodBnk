<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloodComponent extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'shelf_life_days'
    ];

    public function inventory(): HasMany
    {
        return $this->hasMany(BloodInventory::class, 'component_id');
    }
} 