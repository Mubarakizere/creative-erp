<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentationCategory;
use App\Models\DocumentationArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentationController extends Controller
{
    public function index()
    {
        $categories = DocumentationCategory::where('is_active', true)
            ->with(['articles' => function ($query) {
                $query->where('status', 'published')->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return view('admin.documentation.index', compact('categories'));
    }

    public function show($categorySlug, $articleSlug)
    {
        $category = DocumentationCategory::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $article = DocumentationArticle::where('documentation_category_id', $category->id)
            ->where('slug', $articleSlug)
            ->where('status', 'published')
            ->firstOrFail();

        $categories = DocumentationCategory::where('is_active', true)
            ->with(['articles' => function ($query) {
                $query->where('status', 'published')->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        // Convert markdown to HTML
        $content = Str::markdown($article->content ?? '');

        return view('admin.documentation.show', compact('article', 'category', 'categories', 'content'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $articles = collect();
        if ($query) {
            $articles = DocumentationArticle::where('status', 'published')
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('content', 'like', "%{$query}%");
                })
                ->with('category')
                ->orderBy('title')
                ->get();
        }

        return view('admin.documentation.search', compact('articles', 'query'));
    }
}
