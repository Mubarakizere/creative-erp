<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Upload a new document.
     */
    public function uploadDocument(array $data, UploadedFile $file, Model $documentable): Document
    {
        $this->validateFileSize($file);
        
        $extension = $file->getClientOriginalExtension();
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        
        $folder = $data['folder'] ?? $this->getDefaultFolder($documentable);
        
        $filename = Str::uuid() . '.' . $extension;
        $path = $file->storeAs('documents/' . $folder, $filename, 'public');
        
        $document = Document::create([
            'documentable_type' => get_class($documentable),
            'documentable_id' => $documentable->id,
            'category_id' => $data['category_id'] ?? null,
            'folder' => $folder,
            'file_name' => $filename,
            'original_name' => $originalName,
            'extension' => $extension,
            'mime_type' => $mimeType,
            'size' => $size,
            'disk' => 'public',
            'path' => $path,
            'version' => 1,
            'visibility' => $data['visibility'] ?? 'Internal',
            'description' => $data['description'] ?? null,
            'uploaded_by' => auth()->id(),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
        
        // Activity/Audit Preparation
        // event(new \App\Events\DocumentUploaded($document));

        return $document;
    }

    /**
     * Replace an existing document with a new file.
     */
    public function replaceDocument(Document $document, UploadedFile $file, array $data = []): Document
    {
        $this->validateFileSize($file);
        
        // Delete old file from storage (or keep for history if needed, but for now we replace)
        // Note: The requirements state "Every uploaded document should keep its history."
        // We will increment the version. For a true version control, we'd need a DocumentVersion model.
        // The prompt says "Prepare architecture only. Allow restoring previous versions in future."
        // So we just replace the file for now and increment version, or keep the old file and just create a new file path.
        
        // Let's keep the old file in storage, and just upload the new one.
        
        $extension = $file->getClientOriginalExtension();
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        
        $filename = Str::uuid() . '.' . $extension;
        $path = $file->storeAs('documents/' . $document->folder, $filename, 'public');
        
        $document->update([
            'file_name' => $filename,
            'original_name' => $originalName,
            'extension' => $extension,
            'mime_type' => $mimeType,
            'size' => $size,
            'path' => $path,
            'version' => $document->version + 1,
            'description' => $data['description'] ?? $document->description,
            'updated_by' => auth()->id(),
        ]);
        
        // Activity/Audit Preparation
        // event(new \App\Events\DocumentReplaced($document));

        return $document;
    }

    /**
     * Update document metadata.
     */
    public function updateMetadata(Document $document, array $data): Document
    {
        $oldCategory = $document->category_id;
        $oldVisibility = $document->visibility;
        
        $document->update($data);
        
        // Activity/Audit Preparation
        if (isset($data['category_id']) && $data['category_id'] != $oldCategory) {
            // event(new \App\Events\DocumentCategoryChanged($document));
        }
        if (isset($data['visibility']) && $data['visibility'] != $oldVisibility) {
            // event(new \App\Events\DocumentVisibilityChanged($document));
        }
        
        // event(new \App\Events\DocumentUpdated($document));
        
        return $document;
    }

    /**
     * Rename a document (changes original_name metadata).
     */
    public function renameDocument(Document $document, string $newName): Document
    {
        $document->update([
            'original_name' => $newName,
            'updated_by' => auth()->id(),
        ]);
        
        return $document;
    }

    /**
     * Delete a document.
     */
    public function deleteDocument(Document $document): bool
    {
        $result = $document->delete();
        
        // Activity/Audit Preparation
        // event(new \App\Events\DocumentDeleted($document));
        
        return $result;
    }

    /**
     * Restore a soft-deleted document.
     */
    public function restoreDocument(Document $document): bool
    {
        $result = $document->restore();
        
        // Activity/Audit Preparation
        // event(new \App\Events\DocumentRestored($document));
        
        return $result;
    }

    /**
     * Validate file size (100MB limit).
     */
    public function validateFileSize(UploadedFile $file): void
    {
        $maxSize = 100 * 1024 * 1024; // 100 MB
        if ($file->getSize() > $maxSize) {
            throw new Exception("File size exceeds the maximum limit of 100MB.");
        }
    }

    /**
     * Get a default folder based on the model.
     */
    protected function getDefaultFolder(Model $documentable): string
    {
        $class = class_basename($documentable);
        return strtolower(Str::plural($class)) . '/' . $documentable->id;
    }
    
    /**
     * Get preview URL
     */
    public function getPreviewUrl(Document $document): string
    {
        // Currently returning the direct asset url.
        // Can be extended with temporary signed routes for private documents.
        return asset('storage/' . $document->path);
    }
    
    /**
     * Get download URL
     */
    public function getDownloadUrl(Document $document): string
    {
        return route('admin.documents.download', $document->id);
    }
}
