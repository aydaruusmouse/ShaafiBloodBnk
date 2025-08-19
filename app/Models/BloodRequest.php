<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToHospital;

class BloodRequest extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'department_id',
        'patient_id',
        'patient_name',
        'blood_group',
        'units_required',
        'required_date',
        'urgency',
        'notes',
        'status',
        'requested_by'
    ];

    protected $casts = [
        'required_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'urgent' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-blue-100 text-blue-800'
        };
    }

    public function crossMatches(): HasMany
    {
        return $this->hasMany(CrossMatch::class);
    }

    public function transfusions(): HasMany
    {
        return $this->hasMany(Transfusion::class);
    }
}
