<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToHospital;

class LabTest extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'donor_id',
        'hiv',
        'hepatitis_b',
        'hepatitis_c',
        'syphilis',
        'tested_by',
        'test_date',
        'notes'
    ];

    protected $casts = [
     'test_date' => 'datetime'
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }
}
