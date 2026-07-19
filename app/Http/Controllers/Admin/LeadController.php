<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\LeadService;
use App\Services\Crm\CustomerTimelineService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeadController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected LeadService $leadService)
    {
        $this->authorizeResource(Lead::class, 'lead');
    }

    public function index(Request $request)
    {
        $leads = $this->leadService->getPaginatedLeads($request->all());
        return view('admin.crm.leads.index', compact('leads'));
    }

    public function create()
    {
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.crm.leads.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $lead = $this->leadService->createLead($request->all());
        return redirect()->route('admin.crm.leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['owner', 'leadSource', 'industry', 'tags', 'activities', 'documents', 'comments']);
        return view('admin.crm.leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.crm.leads.edit', compact('lead', 'companies'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->leadService->updateLead($lead, $request->all());
        return redirect()->route('admin.crm.leads.show', $lead)->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $this->leadService->deleteLead($lead);
        return redirect()->route('admin.crm.leads.index')->with('success', 'Lead archived successfully.');
    }

    public function convert(Request $request, Lead $lead)
    {
        $this->authorize('convert', $lead);
        
        $result = $this->leadService->convertLead($lead, $request->all());
        
        return redirect()->route('admin.crm.opportunities.show', $result['opportunity']->id ?? $result['account']->id)
                         ->with('success', 'Lead converted successfully.');
    }

    public function timeline(Lead $lead, CustomerTimelineService $timelineService)
    {
        $this->authorize('view', $lead);
        
        $timelineEvents = $timelineService->getForModel($lead);
        return view('admin.crm.partials.timeline', compact('timelineEvents'));
    }
}
