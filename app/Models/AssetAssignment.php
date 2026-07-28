<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'department_id',
        'branch_id',
        'project_id',
        'location',
        'assigned_at',
        'returned_at',
        'notes',
        'assigned_by',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function assignedBy() { return $this->belongsTo(User::class, 'assigned_by'); }
}
