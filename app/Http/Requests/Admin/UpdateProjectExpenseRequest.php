<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized in controller policy check
    }

    public function rules(): array
    {
        return [
            'category' => 'sometimes|required|string|max:100',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'expense_date' => 'sometimes|required|date',
            'vendor_name' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'payment_status' => 'sometimes|required|string|in:Paid,Pending,Reimbursement',
            'payment_method' => 'nullable|string|max:50',
            'receipt' => 'nullable|file|mimes:pdf,png,jpg,jpeg,webp,doc,docx|max:10240',
        ];
    }
}
