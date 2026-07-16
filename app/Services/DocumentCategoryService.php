<?php

namespace App\Services;

use App\Models\DocumentCategory;
use Illuminate\Support\Str;

class DocumentCategoryService
{
    /**
     * Create a new document category.
     */
    public function createCategory(array $data): DocumentCategory
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return DocumentCategory::create($data);
    }

    /**
     * Update an existing document category.
     */
    public function updateCategory(DocumentCategory $category, array $data): DocumentCategory
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        $category->update($data);
        return $category;
    }

    /**
     * Delete a document category.
     */
    public function deleteCategory(DocumentCategory $category): bool
    {
        return $category->delete();
    }

    /**
     * Restore a soft-deleted document category.
     */
    public function restoreCategory(DocumentCategory $category): bool
    {
        return $category->restore();
    }
}
