<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delegation extends Model
{
    use HasFactory;

    protected $fillable = [
        'delegator_id', 'delegate_id', 'start_date', 'end_date', 'permissions',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'permissions' => 'array',
    ];

    public function delegator()
    {
        return $this->belongsTo(User::class, 'delegator_id');
    }

    public function delegate()
    {
        return $this->belongsTo(User::class, 'delegate_id');
    }

    public function getStatusAttribute(): string
    {
        $now = now();
        if ($now->lt($this->start_date)) return 'scheduled';
        if ($now->gt($this->end_date)) return 'expired';
        return 'active';
    }
}
