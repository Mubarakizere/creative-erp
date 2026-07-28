<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectMaterialIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create_project_material_issue');
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'issue_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('project_material_issues')->where(function ($query) {
                    return $query->where('company_id', $this->user()->company_id);
                })
            ],
            'issue_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.project_material_request_item_id' => ['nullable', 'exists:project_material_request_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
