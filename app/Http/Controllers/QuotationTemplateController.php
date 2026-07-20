<?php

namespace App\Http\Controllers;

use App\Models\QuotationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuotationTemplateController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', QuotationTemplate::class);
        return response()->json(QuotationTemplate::where('company_id', $request->user()->company_id ?? 1)->get());
    }

    public function store(Request $request)
    {
        Gate::authorize('create', QuotationTemplate::class);
        $data = $request->validate([
            'name' => 'required|string',
            'header_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'default_payment_term_id' => 'nullable|exists:payment_terms,id',
            'is_active' => 'boolean'
        ]);
        $data['company_id'] = $request->user()->company_id ?? 1;

        $template = QuotationTemplate::create($data);
        return response()->json($template, 201);
    }

    public function show(QuotationTemplate $quotationTemplate)
    {
        Gate::authorize('view', $quotationTemplate);
        return response()->json($quotationTemplate);
    }

    public function update(Request $request, QuotationTemplate $quotationTemplate)
    {
        Gate::authorize('update', $quotationTemplate);
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'header_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'default_payment_term_id' => 'nullable|exists:payment_terms,id',
            'is_active' => 'boolean'
        ]);

        $quotationTemplate->update($data);
        return response()->json($quotationTemplate);
    }

    public function destroy(QuotationTemplate $quotationTemplate)
    {
        Gate::authorize('delete', $quotationTemplate);
        $quotationTemplate->delete();
        return response()->json(null, 204);
    }
}
