<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = ['workflow_id', 'approver_role', 'level'];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
}
