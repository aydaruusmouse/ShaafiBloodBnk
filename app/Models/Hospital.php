<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
 

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'email',
        'status'
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function bloodRequests(): HasManyThrough
    {
        return $this->hasManyThrough(BloodRequest::class, Department::class);
    }

    public function transfusions(): HasMany
    {
        return $this->hasMany(Transfusion::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Hospitals are global tenants; do NOT scope them by hospital_id
}
