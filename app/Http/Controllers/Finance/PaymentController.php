<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Finance\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\BankAccount;
use App\Models\Project;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $companyId = auth()->user()->company_id ?? 1;

        $query = Payment::where('company_id', $companyId)
            ->with(['client', 'project', 'paymentMethod', 'bankAccount', 'company']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQ) use ($search) {
                      $clientQ->where('display_name', 'like', "%{$search}%")
                              ->orWhere('company_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function ($projQ) use ($search) {
                      $projQ->where('name', 'like', "%{$search}%")
                            ->orWhere('project_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('allocations.invoice', function ($invQ) use ($search) {
                      $invQ->where('invoice_number', 'like', "%{$search}%");
                  });
            });
        }

        // Filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // Summary stats
        $baseStatsQuery = Payment::where('company_id', $companyId);
        $totalCount = (clone $baseStatsQuery)->count();
        $totalCollected = (clone $baseStatsQuery)->sum('amount');
        $thisMonthCollected = (clone $baseStatsQuery)
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        $avgPayment = $totalCount > 0 ? ($totalCollected / $totalCount) : 0;

        $stats = [
            'total_count' => $totalCount,
            'total_collected' => $totalCollected,
            'this_month_collected' => $thisMonthCollected,
            'avg_payment' => $avgPayment,
        ];

        // Filter dropdown options
        $projects = Project::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name', 'project_code']);

        $clients = Client::where('company_id', $companyId)
            ->orderBy('display_name')
            ->get(['id', 'display_name', 'company_name', 'first_name', 'last_name']);

        $paymentMethods = PaymentMethod::orderBy('name')->get(['id', 'name']);

        $payments = $query->latest('payment_date')->latest('id')->paginate(15)->withQueryString();

        return view('admin.finance.payments.index', compact('payments', 'stats', 'projects', 'clients', 'paymentMethods'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Payment::class);

        $companyId = auth()->user()->company_id ?? 1;

        $clients = Client::where('company_id', $companyId)->orderBy('display_name')->get();
        $projects = Project::where('company_id', $companyId)
            ->whereNotIn('status', ['Cancelled', 'Closed'])
            ->with(['client:id,name', 'company:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'project_code', 'client_id', 'company_id']);

        $paymentMethods = PaymentMethod::all();
        $bankAccounts = BankAccount::where('company_id', $companyId)->get();
        
        $preselectedInvoice = null;
        $preselectedProjectId = $request->input('project_id');

        if ($request->has('invoice_id')) {
            $preselectedInvoice = Invoice::with(['client', 'project'])->findOrFail($request->invoice_id);
            if ($preselectedInvoice->balance_due <= 0) {
                return redirect()->route('admin.finance.invoices.show', $preselectedInvoice)
                                 ->with('error', 'This invoice is already fully paid.');
            }
            if ($preselectedInvoice->project_id && !$preselectedProjectId) {
                $preselectedProjectId = $preselectedInvoice->project_id;
            }
        }

        // Fetch open invoices with client and project
        $openInvoices = Invoice::where('company_id', $companyId)
                               ->where('balance_due', '>', 0)
                               ->whereNotIn('status', ['Draft', 'Cancelled'])
                               ->with(['project:id,name,project_code', 'client:id,name'])
                               ->orderBy('issue_date')
                               ->get();

        $payment_number = app(\App\Services\SequenceService::class)->generate('payment', $companyId);

        return view('admin.finance.payments.create', compact(
            'clients',
            'projects',
            'paymentMethods',
            'bankAccounts',
            'preselectedInvoice',
            'preselectedProjectId',
            'openInvoices',
            'payment_number'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Payment::class);
        
        $request->validate([
            'payment_number' => 'required|string|unique:payments,payment_number',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reference_number' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'notes' => 'nullable|string',
            'allocations' => 'required|array|min:1',
            'allocations.*.invoice_id' => 'required|exists:invoices,id',
            'allocations.*.amount' => 'required|numeric|min:0.01',
        ]);

        $data = $request->except(['allocations', 'reference']);
        $data['company_id'] = auth()->user()->company_id ?? 1;
        
        // Ensure reference_number is populated from reference or payment_number fallback
        $refNum = $request->input('reference_number') ?? $request->input('reference');
        $data['reference_number'] = !empty($refNum) ? $refNum : $data['payment_number'];
        
        $allocations = $request->input('allocations', []);
        
        // Sum total allocated to ensure it matches the total payment amount
        $totalAllocated = collect($allocations)->sum('amount');
        if (round((float) $totalAllocated, 2) !== round((float) $data['amount'], 2)) {
            return redirect()->back()->withInput()->with('error', 'Allocated amounts must equal the total payment amount.');
        }

        $payment = $this->paymentService->processPayment($data, $allocations);
        
        return redirect()->route('admin.finance.payments.show', $payment)
                         ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        $payment->load(['client', 'project', 'paymentMethod', 'bankAccount', 'allocations.invoice.project', 'receipt', 'creator']);
        return view('admin.finance.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        $this->authorize('delete', $payment);
        
        $affectedInvoices = $payment->allocations()->pluck('invoice_id')->unique();
        
        $payment->delete();

        // Recalculate affected invoices using InvoiceService
        $invoiceService = app(\App\Services\Finance\InvoiceService::class);
        foreach ($affectedInvoices as $invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice) {
                $invoiceService->calculateTotals($invoice);
            }
        }

        return redirect()->route('admin.finance.payments.index')->with('success', 'Payment deleted successfully. Affected invoices recalculated.');
    }
}
