<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteProject extends Model
{
    protected $fillable = [
        'title',
        'category',
        'description',
        'image',
        'sort_order',
        'is_active',
    ];
}
