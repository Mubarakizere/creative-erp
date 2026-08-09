<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentationCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function articles()
    {
        return $this->hasMany(DocumentationArticle::class)->orderBy('order');
    }
}
