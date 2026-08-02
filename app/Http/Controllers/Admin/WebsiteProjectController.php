<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebsiteProject;

class WebsiteProjectController extends Controller
{
    public function index()
    {
        $projects = WebsiteProject::orderBy('sort_order')->get();
        return view('admin.website_projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.website_projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('website', 'public');
            $data['image'] = '/storage/' . $path;
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->image_url;
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->has('is_active');

        WebsiteProject::create($data);

        return redirect()->route('admin.website-projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(WebsiteProject $websiteProject)
    {
        return view('admin.website_projects.edit', compact('websiteProject'));
    }

    public function update(Request $request, WebsiteProject $websiteProject)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('website', 'public');
            $data['image'] = '/storage/' . $path;
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->image_url;
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->has('is_active');

        $websiteProject->update($data);

        return redirect()->route('admin.website-projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(WebsiteProject $websiteProject)
    {
        $websiteProject->delete();
        return back()->with('success', 'Project deleted successfully.');
    }
}
