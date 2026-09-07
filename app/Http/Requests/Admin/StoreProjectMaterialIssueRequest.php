<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectMaterialIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('material_issue.create');
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'project_material_request_id' => ['required', 'exists:project_material_requests,id'],
            'task_id' => ['nullable', 'exists:tasks,id'],
            'issue_number' => ['nullable', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.project_material_request_item_id' => ['nullable', 'exists:project_material_request_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
