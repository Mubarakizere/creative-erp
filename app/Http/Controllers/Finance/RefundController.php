<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Services\Finance\RefundService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Client;
use App\Models\Payment;

class RefundController extends Controller
{
    use AuthorizesRequests;

    protected RefundService $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    public function index()
    {
        $this->authorize('viewAny', Refund::class);
        $refunds = Refund::with(['client', 'payment'])->where('company_id', auth()->user()->company_id ?? 1)->latest()->paginate(15);
        return view('admin.finance.refunds.index', compact('refunds'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Refund::class);
        $clients = Client::where('company_id', auth()->user()->company_id ?? 1)->get();
        
        $preselectedPayment = null;
        if ($request->has('payment_id')) {
            $preselectedPayment = Payment::findOrFail($request->payment_id);
        }
        
        return view('admin.finance.refunds.create', compact('clients', 'preselectedPayment'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Refund::class);
        
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'refund_date' => 'required|date',
            'reason' => 'required|string',
            'payment_id' => 'nullable|exists:payments,id',
            'refund_method' => 'required|string'
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id ?? 1;
        
        $refund = $this->refundService->processRefund($data);
        
        return redirect()->route('admin.finance.refunds.show', $refund)
                         ->with('success', 'Refund processed successfully.');
    }

    public function show(Refund $refund)
    {
        $this->authorize('view', $refund);
        $refund->load(['client', 'payment']);
        return view('admin.finance.refunds.show', compact('refund'));
    }
}
