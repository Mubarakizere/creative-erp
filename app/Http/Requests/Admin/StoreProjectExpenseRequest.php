<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized in controller policy check
    }

    public function rules(): array
    {
        return [
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'vendor_name' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'payment_status' => 'required|string|in:Paid,Pending,Reimbursement',
            'payment_method' => 'nullable|string|max:50',
            'receipt' => 'nullable|file|mimes:pdf,png,jpg,jpeg,webp,doc,docx|max:10240',
        ];
    }
}
