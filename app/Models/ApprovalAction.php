<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_id',
        'workflow_step_id',
        'user_id',
        'action',
        'comment',
        'ip_address',
        'user_agent',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function approval()
    {
        return $this->belongsTo(Approval::class);
    }

    public function step()
    {
        return $this->belongsTo(WorkflowStep::class, 'workflow_step_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
