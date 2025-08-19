<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToHospital;

class BloodInventory extends Model
{
    use HasFactory, SoftDeletes, BelongsToHospital;

    protected $table = 'blood_inventory';

    protected $fillable = [
        'hospital_id',
        'batch_number',
        'blood_group',
        'component_id',
        'donor_id',
        'storage_location_id',
        'status',
        'collection_date',
        'expiry_date',
        'barcode',
        'notes',
        'test_status',
        'test_date',
        'tested_by'
    ];

    protected $casts = [
        'collection_date' => 'date',
        'expiry_date' => 'date',
        'test_date' => 'date'
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(BloodComponent::class, 'component_id');
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class, 'storage_location_id');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && !$this->isExpired();
    }

    public function requests()
    {
        return $this->hasMany(BloodRequest::class);
    }
}
