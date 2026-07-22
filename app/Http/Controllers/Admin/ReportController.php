<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportTemplate;
use App\Models\FavoriteReport;
use App\Services\ReportService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    protected ReportService $reportService;
    protected ExportService $exportService;

    public function __construct(ReportService $reportService, ExportService $exportService)
    {
        $this->reportService = $reportService;
        $this->exportService = $exportService;
    }

    public function index()
    {
        Gate::authorize('viewAny', ReportTemplate::class);

        $userId = auth()->id();
        $systemTemplates = $this->reportService->getSystemTemplates();
        $userTemplates = $this->reportService->getUserTemplates($userId);
        $favoriteTemplates = $this->reportService->getFavoriteTemplates($userId);

        return view('admin.reports.index', compact('systemTemplates', 'userTemplates', 'favoriteTemplates'));
    }

    public function builder(Request $request)
    {
        Gate::authorize('create', ReportTemplate::class);

        // Can pass existing template if editing
        $template = null;
        if ($request->has('template_id')) {
            $template = ReportTemplate::findOrFail($request->template_id);
            Gate::authorize('view', $template);
        }

        $options = [
            'companies' => \App\Models\Company::select('id', 'name')->get(),
            'branches' => \App\Models\Branch::select('id', 'name', 'company_id')->get(),
            'departments' => \App\Models\Department::select('id', 'name', 'branch_id')->get(),
            'projects' => \App\Models\Project::select('id', 'name')->get(),
            'clients' => \App\Models\Client::select('id', 'name')->get(),
            'users' => \App\Models\User::select('id', 'name', 'department_id')->get(),
        ];

        return view('admin.reports.builder', compact('template', 'options'));
    }

    public function preview(Request $request)
    {
        Gate::authorize('create', ReportTemplate::class);

        $type = $request->input('type');
        $filters = $request->input('filters', []);
        
        // We create a dummy template in memory to pass to getReportData
        $template = new ReportTemplate([
            'name' => 'Preview',
            'type' => $type,
            'filters' => $filters,
            'layout' => $request->input('layout', []),
        ]);

        $reportData = $this->reportService->getReportData($template, $filters);
        
        // Return a partial view or JSON. Let's return JSON for Alpine to render or HTML.
        // For simplicity, returning rendered HTML of just the viewer content.
        return view('admin.reports.partials.viewer-content', $reportData)->render();
    }

    public function show(Request $request, ReportTemplate $reportTemplate)
    {
        Gate::authorize('view', $reportTemplate);

        $filters = $request->except(['_token', 'page']);
        $reportData = $this->reportService->getReportData($reportTemplate, $filters);

        $this->reportService->logActivity('report_generated', $reportTemplate);

        return view('admin.reports.viewer', $reportData);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', ReportTemplate::class);

        if (is_string($request->input('layout'))) {
            $request->merge(['layout' => json_decode($request->input('layout'), true)]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'filters' => 'nullable|array',
            'layout' => 'nullable|array',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['company_id'] = auth()->user()->company_id;
        $validated['is_system'] = false;

        $template = ReportTemplate::create($validated);
        
        $this->reportService->logActivity('template_created', $template);

        return redirect()->route('admin.reports.show', $template)->with('success', 'Report Template saved successfully.');
    }

    public function update(Request $request, ReportTemplate $reportTemplate)
    {
        Gate::authorize('update', $reportTemplate);

        if (is_string($request->input('layout'))) {
            $request->merge(['layout' => json_decode($request->input('layout'), true)]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'filters' => 'nullable|array',
            'layout' => 'nullable|array',
        ]);

        $reportTemplate->update($validated);

        return redirect()->route('admin.reports.show', $reportTemplate)->with('success', 'Report Template updated successfully.');
    }

    public function destroy(ReportTemplate $reportTemplate)
    {
        Gate::authorize('delete', $reportTemplate);

        $reportTemplate->delete();

        return redirect()->route('admin.reports.index')->with('success', 'Report Template deleted successfully.');
    }

    public function favorite(ReportTemplate $reportTemplate)
    {
        Gate::authorize('view', $reportTemplate);

        $favorite = FavoriteReport::where('user_id', auth()->id())
            ->where('report_template_id', $reportTemplate->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return back()->with('success', 'Report removed from favorites.');
        }

        FavoriteReport::create([
            'user_id' => auth()->id(),
            'report_template_id' => $reportTemplate->id,
        ]);

        return back()->with('success', 'Report added to favorites.');
    }

    public function export(Request $request, ReportTemplate $reportTemplate)
    {
        Gate::authorize('export', $reportTemplate);

        $validated = $request->validate([
            'format' => 'required|in:pdf,xlsx,csv',
        ]);

        $filters = $request->except(['_token', 'format']);
        
        $exportHistory = $this->exportService->export(
            $reportTemplate,
            $filters,
            $validated['format'],
            auth()->id(),
            auth()->user()->company_id
        );

        $this->reportService->logActivity('report_exported', $reportTemplate);

        $exportHistory->refresh();
        if ($exportHistory->status === 'completed' && $exportHistory->file_path) {
            return \Illuminate\Support\Facades\Storage::disk('local')->download($exportHistory->file_path);
        }

        return back()->with('error', 'Export failed: ' . $exportHistory->error_message);
    }
}
