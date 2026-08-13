<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitOfMeasure extends Model
{
    use HasFactory, SoftDeletes, HasUuidColumn;

    protected $guarded = ['id'];

    public function products() { return $this->hasMany(Product::class); }
}