<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Traits\BelongsToHospital;

class BloodBag extends Model
{
    use HasFactory, BelongsToHospital;

  
    protected $fillable = [
        'hospital_id',
        'donor_id',
        'patient_id',
        'donor_type',
        'serial_number',
        'blood_group',
        'component_type',
        'volume',
        'collection_date',
        'expiry_date',
        'status',
        'collected_by',
        'collection_location',
        'notes',
    ];

    protected $casts = [
        'volume' => 'decimal:2',
        'collection_date' => 'date',
        'expiry_date' => 'date',
        
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_RESERVED = 'reserved';
    const STATUS_TRANSFUSED = 'transfused';
    const STATUS_EXPIRED = 'expired';
    const STATUS_DISCARDED = 'discarded';

    // Donor type constants
    const DONOR_TYPE_VOLUNTEER = 'volunteer';
    const DONOR_TYPE_FAMILY_REPLACEMENT = 'family_replacement';

    // Component type constants
    const COMPONENT_WHOLE_BLOOD = 'whole_blood';
    const COMPONENT_RBC = 'rbc';
    const COMPONENT_PLASMA = 'plasma';
    const COMPONENT_PLATELETS = 'platelets';

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function crossMatches(): HasMany
    {
        return $this->hasMany(CrossMatch::class);
    }

    public function transfusions(): HasMany
    {
        return $this->hasMany(Transfusion::class);
    }

    // Helper methods
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    public function getComponentTypeLabel(): string
    {
        return [
            self::COMPONENT_WHOLE_BLOOD => 'Whole Blood',
            self::COMPONENT_RBC => 'Red Blood Cells',
            self::COMPONENT_PLASMA => 'Plasma',
            self::COMPONENT_PLATELETS => 'Platelets',
        ][$this->component_type] ?? $this->component_type;
    }

    public function getDonorTypeLabel(): string
    {
        return [
            self::DONOR_TYPE_VOLUNTEER => 'Volunteer',
            self::DONOR_TYPE_FAMILY_REPLACEMENT => 'Family Replacement',
        ][$this->donor_type] ?? $this->donor_type;
    }

    public static function generateSerialNumber(): string
    {
        return 'BAG-' . strtoupper(Str::random(8));
    }
}
