<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseAudit extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory, \App\Models\Traits\HasUuidColumn;

    protected $guarded = ['id'];
}
