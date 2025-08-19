<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'blood_type',
        'message_template',
        'type',
        'status',
        'total_recipients',
        'sent_count',
        'failed_count',
        'scheduled_at',
        'completed_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getRecipients()
    {
        $query = Donor::query();
        
        if ($this->blood_type) {
            $query->where('blood_group', $this->blood_type);
        }

        return $query->whereNotNull('Tell')
                    ->where('Tell', '!=', '')
                    ->get();
    }

    public function getSuccessRateAttribute()
    {
        if ($this->total_recipients === 0) {
            return 0;
        }
        return round(($this->sent_count / $this->total_recipients) * 100);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'sending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
