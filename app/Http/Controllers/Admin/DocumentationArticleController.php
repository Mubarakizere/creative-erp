<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentationArticle;
use App\Models\DocumentationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentationArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:documentation.create')->only(['index', 'create', 'store']);
        $this->middleware('can:documentation.update')->only(['edit', 'update']);
        $this->middleware('can:documentation.delete')->only(['destroy']);
    }

    public function index()
    {
        $articles = DocumentationArticle::with('category')->orderBy('documentation_category_id')->orderBy('order')->paginate(20);
        return view('admin.documentation_articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = DocumentationCategory::orderBy('order')->get();
        return view('admin.documentation_articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'documentation_category_id' => 'required|exists:documentation_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:documentation_articles',
            'content' => 'required|string',
            'order' => 'required|integer',
            'status' => 'required|in:draft,published',
        ]);

        DocumentationArticle::create($validated);

        return redirect()->route('admin.documentation-articles.index')
            ->with('success', 'Documentation article created successfully.');
    }

    public function edit(DocumentationArticle $documentationArticle)
    {
        $categories = DocumentationCategory::orderBy('order')->get();
        return view('admin.documentation_articles.edit', compact('documentationArticle', 'categories'));
    }

    public function update(Request $request, DocumentationArticle $documentationArticle)
    {
        $validated = $request->validate([
            'documentation_category_id' => 'required|exists:documentation_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:documentation_articles,slug,' . $documentationArticle->id,
            'content' => 'required|string',
            'order' => 'required|integer',
            'status' => 'required|in:draft,published',
        ]);

        $documentationArticle->update($validated);

        return redirect()->route('admin.documentation-articles.index')
            ->with('success', 'Documentation article updated successfully.');
    }

    public function destroy(DocumentationArticle $documentationArticle)
    {
        $documentationArticle->delete();
        return redirect()->route('admin.documentation-articles.index')
            ->with('success', 'Documentation article deleted successfully.');
    }
}
