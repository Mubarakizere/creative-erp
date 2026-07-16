<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'max:102400', // 100MB
                'mimes:jpg,jpeg,png,gif,webp,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,7z,mp4,mov,avi,mp3,wav'
            ],
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer',
            'category_id' => 'nullable|exists:document_categories,id',
            'visibility' => ['nullable', 'string', Rule::in(['Private', 'Internal', 'Public'])],
            'description' => 'nullable|string',
            'folder' => 'nullable|string|max:255',
        ];
    }
}
