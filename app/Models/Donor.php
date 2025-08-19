<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToHospital;

class Donor extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'sex',
        'age',
        'occupation',
        'village',
        'tell',
        'weight',
        'bp',
        'hemoglobin',
        'screening',
        'type_of_donation',
        'blood_group',
        'checked_by',
        'is_eligible',
        'status'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'age' => 'integer',
        'weight' => 'float',
        'is_eligible' => 'boolean',
        'hemoglobin' => 'float'
        
    ];
    public function setHemoglobinAttribute($value)
    {
        $this->attributes['hemoglobin'] = $value === '' ? null : $value;
    }
    
    
    // and keep your cast:
   
    
    public function labTests(): HasMany
    {
        return $this->hasMany(LabTest::class);
    }

    public function bloodBags(): HasMany
    {
        return $this->hasMany(BloodBag::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }
}
