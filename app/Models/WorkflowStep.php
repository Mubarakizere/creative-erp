<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_workflow_id',
        'step_order',
        'name',
        'approver_role_id',
        'approver_user_id',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function workflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'approval_workflow_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'approver_role_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
