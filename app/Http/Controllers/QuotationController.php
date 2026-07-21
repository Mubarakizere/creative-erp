<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Account;
use App\Models\Opportunity;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Tax;
use App\Models\PaymentTerm;
use App\Models\QuotationTemplate;
use App\Models\Company;
use App\Services\QuotationService;
use App\Services\QuotationApprovalService;
use App\Services\ExportService;
use App\Services\CrmActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuotationController extends Controller
{
    protected $quotationService;
    protected $approvalService;
    protected $exportService;
    protected CrmActivityService $activityService;

    public function __construct(QuotationService $quotationService, QuotationApprovalService $approvalService, ExportService $exportService, CrmActivityService $activityService)
    {
        $this->quotationService = $quotationService;
        $this->approvalService = $approvalService;
        $this->exportService = $exportService;
        $this->activityService = $activityService;
    }

    protected function logActivity(Quotation $quotation, string $subject, string $description, string $type = 'System')
    {
        $this->activityService->createActivity([
            'company_id' => $quotation->company_id,
            'activityable_type' => Quotation::class,
            'activityable_id' => $quotation->id,
            'type' => $type,
            'subject' => $subject,
            'description' => $description,
            'status' => 'Completed',
            'created_by' => auth()->id(),
        ]);
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Quotation::class);
        $query = Quotation::where('company_id', $request->user()->company_id ?? 1)
                               ->with(['items', 'status', 'owner', 'account', 'opportunity', 'lead']);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('account', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('contact', function($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('lead', function($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('status', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $quotations = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('admin.crm.quotations.index', compact('quotations'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Quotation::class);
        $companyId = $request->user()->company_id ?? 1;

        $accounts = Account::where('company_id', $companyId)->orderBy('name')->get();
        $opportunities = Opportunity::where('company_id', $companyId)->orderBy('name')->get();
        $leads = Lead::where('company_id', $companyId)->orderBy('last_name')->get();
        $contacts = Contact::where('company_id', $companyId)->orderBy('last_name')->get();
        $taxes = Tax::where('company_id', $companyId)->get();
        $paymentTerms = PaymentTerm::where('company_id', $companyId)->get();
        $templates = QuotationTemplate::where('company_id', $companyId)->get();

        $selectedOpportunityId = $request->query('opportunity_id');
        $selectedAccountId = $request->query('account_id');

        return view('admin.crm.quotations.create', compact(
            'accounts', 'opportunities', 'leads', 'contacts', 'taxes', 'paymentTerms', 'templates', 'selectedOpportunityId', 'selectedAccountId'
        ));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Quotation::class);
        $data = $request->validate([
            'reference' => 'nullable|string',
            'lead_id' => 'nullable|exists:leads,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'account_id' => 'nullable|exists:accounts,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'template_id' => 'nullable|exists:quotation_templates,id',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'owner_id' => 'nullable|exists:users,id',
            'currency' => 'nullable|string',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.unit_price' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'items.*.discount_type' => 'nullable|string|in:fixed,percentage',
            'items.*.tax_id' => 'nullable|exists:taxes,id',
        ]);
        $data['company_id'] = $request->user()->company_id ?? 1;
        $data['quotation_number'] = 'QT-' . strtoupper(uniqid());

        $items = $data['items'];
        unset($data['items']);

        $quotation = $this->quotationService->createQuotation($data, $items);
        $this->logActivity($quotation, 'Quotation Created', "Quotation {$quotation->quotation_number} was created.");
        
        return redirect()->route('admin.crm.quotations.show', $quotation)
                         ->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        Gate::authorize('view', $quotation);
        $quotation->load(['items.tax', 'status', 'owner', 'account', 'opportunity', 'lead', 'contact', 'paymentTerm']);
        return view('admin.crm.quotations.show', compact('quotation'));
    }

    public function edit(Request $request, Quotation $quotation)
    {
        Gate::authorize('update', $quotation);
        $companyId = $request->user()->company_id ?? 1;

        $quotation->load(['items.tax']);

        $accounts = Account::where('company_id', $companyId)->orderBy('name')->get();
        $opportunities = Opportunity::where('company_id', $companyId)->orderBy('name')->get();
        $leads = Lead::where('company_id', $companyId)->orderBy('last_name')->get();
        $contacts = Contact::where('company_id', $companyId)->orderBy('last_name')->get();
        $taxes = Tax::where('company_id', $companyId)->get();
        $paymentTerms = PaymentTerm::where('company_id', $companyId)->get();
        $templates = QuotationTemplate::where('company_id', $companyId)->get();

        return view('admin.crm.quotations.edit', compact(
            'quotation', 'accounts', 'opportunities', 'leads', 'contacts', 'taxes', 'paymentTerms', 'templates'
        ));
    }

    public function update(Request $request, Quotation $quotation)
    {
        Gate::authorize('update', $quotation);
        $data = $request->validate([
            'reference' => 'nullable|string',
            'template_id' => 'nullable|exists:quotation_templates,id',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:quotation_items,id',
            'items.*.product_name' => 'required_with:items|string',
            'items.*.quantity' => 'required_with:items|numeric',
            'items.*.unit_price' => 'required_with:items|numeric',
            'items.*.discount' => 'nullable|numeric',
            'items.*.discount_type' => 'nullable|string|in:fixed,percentage',
            'items.*.tax_id' => 'nullable|exists:taxes,id',
        ]);

        $items = $data['items'] ?? null;
        unset($data['items']);

        $quotation = $this->quotationService->updateQuotation($quotation, $data, $items);
        $this->logActivity($quotation, 'Quotation Updated', "Quotation {$quotation->quotation_number} was updated.");
        
        return redirect()->route('admin.crm.quotations.show', $quotation)
                         ->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Request $request, Quotation $quotation)
    {
        Gate::authorize('delete', $quotation);
        
        if ($request->has('force')) {
            $this->logActivity($quotation, 'Quotation Deleted', "Quotation {$quotation->quotation_number} was permanently deleted.");
            $quotation->forceDelete();
            return redirect()->route('admin.crm.quotations.index')
                             ->with('success', 'Quotation permanently deleted.');
        }

        $this->logActivity($quotation, 'Quotation Archived', "Quotation {$quotation->quotation_number} was archived.");
        $quotation->delete();
        
        return redirect()->route('admin.crm.quotations.index')
                         ->with('success', 'Quotation archived successfully.');
    }

    public function restore($id)
    {
        $quotation = Quotation::withTrashed()->findOrFail($id);
        Gate::authorize('restore', $quotation);
        $quotation->restore();
        $this->logActivity($quotation, 'Quotation Restored', "Quotation {$quotation->quotation_number} was restored from archive.");
        
        return back()->with('success', 'Quotation restored successfully.');
    }

    // Additional Actions
    public function duplicate(Quotation $quotation)
    {
        Gate::authorize('create', Quotation::class);
        $newQuotation = $this->quotationService->createQuotation(
            array_merge($quotation->toArray(), ['quotation_number' => 'QT-' . strtoupper(uniqid())]),
            $quotation->items->toArray()
        );
        return redirect()->route('admin.crm.quotations.show', $newQuotation)
                         ->with('success', 'Quotation duplicated successfully.');
    }

    public function submit(Request $request, Quotation $quotation)
    {
        Gate::authorize('update', $quotation); // usually same as update
        try {
            $this->approvalService->submitForApproval($quotation, $request->user()->id);
            $this->logActivity($quotation, 'Submitted for Approval', "Quotation {$quotation->quotation_number} was submitted for approval.");
            return back()->with('success', 'Quotation submitted for approval.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, Quotation $quotation)
    {
        Gate::authorize('approve', $quotation);
        try {
            $this->approvalService->approve($quotation, $request->user()->id, $request->input('comments'));
            $this->logActivity($quotation, 'Quotation Approved', "Quotation {$quotation->quotation_number} was approved.");
            return back()->with('success', 'Quotation approved.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Quotation $quotation)
    {
        Gate::authorize('approve', $quotation); // Usually same permission to decide
        try {
            $this->approvalService->reject($quotation, $request->user()->id, $request->input('comments'));
            $this->logActivity($quotation, 'Quotation Rejected', "Quotation {$quotation->quotation_number} was rejected.");
            return back()->with('success', 'Quotation rejected.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function export(Request $request, Quotation $quotation)
    {
        Gate::authorize('export', $quotation);
        $format = $request->input('format', 'pdf');
        // Integrate ExportService
        $file = $this->exportService->exportQuotation($quotation, $format);
        
        $this->logActivity($quotation, 'Quotation Exported', "Quotation {$quotation->quotation_number} was exported as {$format}.");
        
        return response()->download($file);
    }
}
