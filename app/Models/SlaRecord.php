<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaRecord extends Model
{
    use HasFactory;

    protected $fillable = ['leave_request_id', 'deadline', 'breached'];

    protected $casts = [
        'deadline' => 'datetime',
        'breached' => 'boolean',
    ];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function getStatusAttribute(): string
    {
        if ($this->breached) return 'breached';
        $hoursLeft = round(now()->diffInHours($this->deadline, false));
        if ($hoursLeft < 4) return 'warning';
        return 'safe';
    }
}
