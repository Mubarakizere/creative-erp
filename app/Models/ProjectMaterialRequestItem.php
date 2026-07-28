<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProjectMaterialRequestItem extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function request()
    {
        return $this->belongsTo(ProjectMaterialRequest::class, 'project_material_request_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
