<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pipeline;
use App\Services\PipelineService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PipelineController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected PipelineService $pipelineService)
    {
        $this->authorizeResource(Pipeline::class, 'pipeline');
    }

    public function index()
    {
        $pipelines = $this->pipelineService->getPipelines();
        return view('admin.crm.pipelines.index', compact('pipelines'));
    }

    public function create()
    {
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.crm.pipelines.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
        ]);
        $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
        $data['is_default'] = $request->has('is_default');
        $data['is_active'] = $request->has('is_active');

        $pipeline = Pipeline::create($data);

        // Seed default stages
        $pipeline->stages()->createMany([
            ['name' => 'Prospecting', 'probability' => 10, 'order' => 1, 'color' => 'bg-gray-500'],
            ['name' => 'Qualification', 'probability' => 20, 'order' => 2, 'color' => 'bg-blue-500'],
            ['name' => 'Needs Analysis', 'probability' => 40, 'order' => 3, 'color' => 'bg-indigo-500'],
            ['name' => 'Value Proposition', 'probability' => 60, 'order' => 4, 'color' => 'bg-purple-500'],
            ['name' => 'Negotiation', 'probability' => 80, 'order' => 5, 'color' => 'bg-yellow-500'],
        ]);

        return redirect()->route('admin.crm.pipelines.index')->with('success', 'Pipeline created successfully with standard stages.');
    }

    public function show(Pipeline $pipeline)
    {
        $pipeline->load('stages');
        return view('admin.crm.pipelines.show', compact('pipeline'));
    }

    public function edit(Pipeline $pipeline)
    {
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.crm.pipelines.edit', compact('pipeline', 'companies'));
    }

    public function update(Request $request, Pipeline $pipeline)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $data['is_default'] = $request->has('is_default');
        $data['is_active'] = $request->has('is_active');

        $pipeline->update($data);
        return redirect()->route('admin.crm.pipelines.index')->with('success', 'Pipeline updated successfully.');
    }

    public function destroy(Pipeline $pipeline)
    {
        $pipeline->delete();
        return redirect()->route('admin.crm.pipelines.index')->with('success', 'Pipeline archived successfully.');
    }
}
