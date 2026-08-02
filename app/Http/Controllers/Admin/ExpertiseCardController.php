<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ExpertiseCard;

class ExpertiseCardController extends Controller
{
    public function index()
    {
        $cards = ExpertiseCard::orderBy('sort_order')->get();
        return view('admin.expertise_cards.index', compact('cards'));
    }

    public function create()
    {
        return view('admin.expertise_cards.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
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

        ExpertiseCard::create($data);

        return redirect()->route('admin.expertise-cards.index')->with('success', 'Expertise card created successfully.');
    }

    public function edit(ExpertiseCard $expertiseCard)
    {
        return view('admin.expertise_cards.edit', compact('expertiseCard'));
    }

    public function update(Request $request, ExpertiseCard $expertiseCard)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
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

        $expertiseCard->update($data);

        return redirect()->route('admin.expertise-cards.index')->with('success', 'Expertise card updated successfully.');
    }

    public function destroy(ExpertiseCard $expertiseCard)
    {
        $expertiseCard->delete();
        return back()->with('success', 'Expertise card deleted successfully.');
    }
}
