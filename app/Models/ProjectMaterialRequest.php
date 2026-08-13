<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuidColumn;
use App\Models\Traits\CompanyScoped;
use App\Models\Traits\BranchScoped;
use App\Traits\LogsActivity;

class ProjectMaterialRequest extends Model
{
    use HasFactory, SoftDeletes, HasUuidColumn, CompanyScoped, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'request_date' => 'date',
        'required_date' => 'date',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function items()
    {
        return $this->hasMany(ProjectMaterialRequestItem::class);
    }

    public function purchaseRequisition()
    {
        return $this->hasOne(PurchaseRequisition::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
