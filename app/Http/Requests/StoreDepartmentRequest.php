<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:departments,name,NULL,id,branch_id,' . $this->input('branch_id'),
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:departments,code,NULL,id,company_id,' . $this->input('company_id'),
            ],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'Please select a company.',
            'company_id.exists' => 'The selected company does not exist.',
            'branch_id.required' => 'Please select a branch.',
            'branch_id.exists' => 'The selected branch does not exist.',
            'name.required' => 'The department name is required.',
            'name.unique' => 'A department with this name already exists in this branch.',
            'code.required' => 'The department code is required.',
            'code.unique' => 'A department with this code already exists in this company.',
            'email.email' => 'Please enter a valid email address.',
            'description.max' => 'The description must not exceed 1000 characters.',
        ];
    }
}
