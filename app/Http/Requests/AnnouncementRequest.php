<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by Controller via Policies
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:info,warning,success,error',
            'priority' => 'required|in:low,normal,high,urgent',
            'audience_type' => 'required|in:entire_system,company,branch,department,role,specific_users',
            'audience_id' => 'nullable|integer|required_unless:audience_type,entire_system,specific_users',
            'user_ids' => 'nullable|array|required_if:audience_type,specific_users',
            'user_ids.*' => 'integer|exists:users,id',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_pinned' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
