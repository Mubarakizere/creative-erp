<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectMaterialIssueItem extends Model
{
    use HasFactory, HasUuidColumn, SoftDeletes;

    protected $guarded = ['id'];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function issue()
    {
        return $this->belongsTo(ProjectMaterialIssue::class, 'project_material_issue_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function materialRequestItem()
    {
        return $this->belongsTo(ProjectMaterialRequestItem::class, 'project_material_request_item_id');
    }
}
