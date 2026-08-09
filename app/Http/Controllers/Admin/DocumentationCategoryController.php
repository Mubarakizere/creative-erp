<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentationCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:documentation.create')->only(['index', 'create', 'store']);
        $this->middleware('can:documentation.update')->only(['edit', 'update']);
        $this->middleware('can:documentation.delete')->only(['destroy']);
    }

    public function index()
    {
        $categories = DocumentationCategory::orderBy('order')->paginate(20);
        return view('admin.documentation_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.documentation_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:documentation_categories',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        DocumentationCategory::create($validated);

        return redirect()->route('admin.documentation-categories.index')
            ->with('success', 'Documentation category created successfully.');
    }

    public function edit(DocumentationCategory $documentationCategory)
    {
        return view('admin.documentation_categories.edit', compact('documentationCategory'));
    }

    public function update(Request $request, DocumentationCategory $documentationCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:documentation_categories,slug,' . $documentationCategory->id,
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $documentationCategory->update($validated);

        return redirect()->route('admin.documentation-categories.index')
            ->with('success', 'Documentation category updated successfully.');
    }

    public function destroy(DocumentationCategory $documentationCategory)
    {
        $documentationCategory->delete();
        return redirect()->route('admin.documentation-categories.index')
            ->with('success', 'Documentation category deleted successfully.');
    }
}
