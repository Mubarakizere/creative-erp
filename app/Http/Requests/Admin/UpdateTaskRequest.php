<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Handled by Policies
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'exists:tasks,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'task_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tasks')->where(function ($query) {
                    return $query->where('project_id', $this->task->project_id);
                })->ignore($this->task->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'string', 'in:Low,Medium,High,Critical'],
            'status' => ['required', 'string', 'in:Pending,In Progress,Waiting Review,Completed,Cancelled'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'start_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
