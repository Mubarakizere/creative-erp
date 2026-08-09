<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentationArticle extends Model
{
    protected $fillable = [
        'documentation_category_id',
        'title',
        'slug',
        'content',
        'order',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(DocumentationCategory::class, 'documentation_category_id');
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }
}
