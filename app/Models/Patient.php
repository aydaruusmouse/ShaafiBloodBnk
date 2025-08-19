<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToHospital;

class Patient extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'name',
        'medical_record_number',
        'blood_group',
        'phone',
        'address',
        'medical_history',
        'age',
        'gender',
    ];

    public function bloodBags(): HasMany
    {
        return $this->hasMany(BloodBag::class);
    }

    public function transfusions(): HasMany
    {
        return $this->hasMany(Transfusion::class);
    }

    public function crossMatches(): HasMany
    {
        return $this->hasMany(CrossMatch::class);
    }

    public function bloodRequests(): HasMany
    {
        return $this->hasMany(BloodRequest::class);
    }

    /**
     * Resolve the model for route model binding
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Ensure tenant context is set before resolving the model
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        return parent::resolveRouteBinding($value, $field);
    }
}
