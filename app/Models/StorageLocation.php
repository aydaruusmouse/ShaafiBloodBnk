<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageLocation extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'temperature',
        'status'
    ];

    public function inventory(): HasMany
    {
        return $this->hasMany(BloodInventory::class, 'storage_location_id');
    }
} 