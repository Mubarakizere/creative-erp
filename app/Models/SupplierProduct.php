<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierProduct extends Model
{
    use HasFactory, SoftDeletes, HasUuidColumn;

    protected $guarded = ['id'];

    public function product() { return $this->belongsTo(Product::class); }
    public function contact() { return $this->belongsTo(Contact::class); }
}