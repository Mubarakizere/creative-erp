<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string'],
            'is_internal' => ['nullable', 'boolean'],
            'parent_id' => ['nullable', 'exists:comments,id'],
            'commentable_type' => ['required', 'string'],
            'commentable_id' => ['required', 'integer'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('is_internal') && !auth()->user()->hasPermissionTo('comment.internal')) {
            $this->merge([
                'is_internal' => false,
            ]);
        }
    }
}
