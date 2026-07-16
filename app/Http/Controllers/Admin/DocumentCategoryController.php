<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentCategoryRequest;
use App\Http\Requests\UpdateDocumentCategoryRequest;
use App\Models\DocumentCategory;
use App\Services\DocumentCategoryService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DocumentCategoryController extends Controller
{
    use AuthorizesRequests;

    protected $categoryService;

    public function __construct(DocumentCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', DocumentCategory::class);

        $categories = DocumentCategory::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('sort_order')
            ->paginate(25)
            ->withQueryString();

        return view('admin.document_categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', DocumentCategory::class);

        return view('admin.document_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentCategoryRequest $request)
    {
        $this->authorize('create', DocumentCategory::class);

        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $this->categoryService->createCategory($data);

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Document category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentCategory $documentCategory)
    {
        $this->authorize('view', $documentCategory);

        $documentCategory->load('creator', 'updater');

        return view('admin.document_categories.show', compact('documentCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentCategory $documentCategory)
    {
        $this->authorize('update', $documentCategory);

        return view('admin.document_categories.edit', compact('documentCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentCategoryRequest $request, DocumentCategory $documentCategory)
    {
        $this->authorize('update', $documentCategory);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $this->categoryService->updateCategory($documentCategory, $data);

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Document category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentCategory $documentCategory)
    {
        $this->authorize('delete', $documentCategory);

        $this->categoryService->deleteCategory($documentCategory);

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Document category deleted successfully.');
    }
}
