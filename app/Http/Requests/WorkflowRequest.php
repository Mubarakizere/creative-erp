<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized via controller/middleware
    }

    public function rules(): array
    {
        return [
            'company_id' => 'nullable|exists:companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module' => 'required|string|max:255',
            'is_active' => 'boolean',
            'steps' => 'required|array|min:1',
            'steps.*.step_order' => 'required|integer|min:1',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.approver_role_id' => 'nullable|exists:roles,id',
            'steps.*.approver_user_id' => 'nullable|exists:users,id',
            'steps.*.is_required' => 'boolean',
        ];
    }
}
