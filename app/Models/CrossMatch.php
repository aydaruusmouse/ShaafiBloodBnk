<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrossMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_request_id',
        'blood_bag_id',
        'patient_id',
        'is_compatible',
        'performed_by',
        'performed_at',
        'notes'
    ];

    protected $casts = [
        'is_compatible' => 'boolean',
        'performed_at' => 'datetime'
    ];

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function bloodBag(): BelongsTo
    {
        return $this->belongsTo(BloodBag::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
