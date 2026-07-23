<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    public function parent() { return $this->belongsTo(ProductCategory::class, 'parent_id'); }
    public function children() { return $this->hasMany(ProductCategory::class, 'parent_id'); }
    public function products() { return $this->hasMany(Product::class); }
}