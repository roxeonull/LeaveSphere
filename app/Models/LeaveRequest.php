<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'leave_type', 'start_date', 'end_date',
        'total_days', 'reason', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const TYPE_ANNUAL = 'Annual Leave';
    const TYPE_SICK = 'Sick Leave';
    const TYPE_PERSONAL = 'Personal Leave';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function slaRecord()
    {
        return $this->hasOne(SlaRecord::class);
    }
}
