<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('project-team.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'department_id' => ['required', 'exists:departments,id'],
            'project_role' => ['required', 'string', 'max:255'],
            'allocation_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'joined_at' => ['required', 'date'],
            'left_at' => ['nullable', 'date', 'after_or_equal:joined_at'],
            'status' => ['required', 'string', 'in:Active,Inactive'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
