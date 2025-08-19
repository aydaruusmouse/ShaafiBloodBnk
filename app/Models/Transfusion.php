<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToHospital;

class Transfusion extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'blood_request_id',
        'blood_bag_id',
        'patient_id',
        'department_id',
        'transfusion_date',
        'reason',
        'notes',
        'performed_by'
    ];

    protected $casts = [
        'transfusion_date' => 'datetime'
    ];

    public function bloodBag(): BelongsTo
    {
        return $this->belongsTo(BloodBag::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
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
