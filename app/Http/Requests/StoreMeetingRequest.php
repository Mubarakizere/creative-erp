<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by MeetingPolicy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'project_id' => 'nullable|exists:projects,id',
            'meeting_type' => 'required|string|in:internal,client,project,hr,training,sales,other',
            'location' => 'nullable|string|max:500',
            'meeting_link' => 'nullable|url|max:500',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'timezone' => 'required|string|max:50',
            'notes' => ['nullable', 'string'],
            'attendees' => ['nullable', 'array'],
            'attendees.*' => ['exists:users,id'],
            'meetingable_type' => ['nullable', 'string'],
            'meetingable_id' => ['nullable', 'integer'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_at.after_or_equal' => 'Meeting start time must be in the future.',
            'end_at.after' => 'End time must be after the start time.',
            'meeting_type.in' => 'Invalid meeting type selected.',
            'meeting_link.url' => 'Meeting link must be a valid URL.',
            'attendees.*.exists' => 'One or more selected attendees do not exist.',
        ];
    }
}
