<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handled by Policies
    }

    public function rules(): array
    {
        $project = $this->route('project');

        return [
            'company_id' => ['required', 'exists:companies,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'project_manager_id' => ['required', 'exists:users,id'],
            'project_code' => [
                'required', 
                'string', 
                'max:50',
                Rule::unique('projects')->where(function ($query) {
                    return $query->where('company_id', $this->company_id);
                })->ignore($project)
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'priority' => ['required', 'string', Rule::in(['Low', 'Medium', 'High', 'Critical'])],
            'status' => ['required', 'string', Rule::in(['Planning', 'Pending', 'In Progress', 'On Hold', 'Completed', 'Cancelled', 'Closed'])],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'estimated_budget' => ['nullable', 'numeric', 'min:0'],
            'actual_budget' => ['nullable', 'numeric', 'min:0'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'start_date' => ['required', 'date'],
            'planned_end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'actual_end_date' => ['nullable', 'date'],
            'contract_number' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
