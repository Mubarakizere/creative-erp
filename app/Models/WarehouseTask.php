<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class WarehouseTask extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory, \App\Models\Traits\HasUuidColumn, \Illuminate\Database\Eloquent\SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    public function taskable()
    {
        return $this->morphTo();
    }
}
