<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShaafiRequest extends Model
{
    protected $fillable = [
        'reference_number',
        'request_type',
        'full_name',
        'mobile_number',
        'blood_group',
        'blood_quantity',
        'city',
        'hospital_id',
        'additional_notes',
        'status',
        'agent_notes',
        'scheduled_at',
        'reviewed_by',
        'reviewed_at',
        'sms_sent_at',
        'sms_last_error',
        'shaafi_user_id',
        'external_reference',
    ];

    protected $casts = [
        'blood_quantity' => 'integer',
        'scheduled_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'sms_sent_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ShaafiRequest $request) {
            if (empty($request->reference_number)) {
                $request->reference_number = 'SR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'approved', 'completed' => 'bg-green-100 text-green-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            'under_review' => 'bg-yellow-100 text-yellow-800',
            'rejected', 'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getRequestTypeLabelAttribute(): string
    {
        return match ($this->request_type) {
            'donation' => 'Blood Donation',
            'blood_request' => 'Blood Request',
            default => ucfirst(str_replace('_', ' ', $this->request_type)),
        };
    }
}
