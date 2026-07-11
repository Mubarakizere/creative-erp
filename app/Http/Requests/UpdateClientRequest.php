<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('client') ? $this->route('client')->id : null;

        return [
            'company_id' => ['required', 'exists:companies,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'client_type' => ['required', 'in:Company,Individual'],
            
            'company_name' => ['required_if:client_type,Company', 'nullable', 'string', 'max:255'],
            'first_name' => ['required_if:client_type,Individual', 'nullable', 'string', 'max:255'],
            'last_name' => ['required_if:client_type,Individual', 'nullable', 'string', 'max:255'],
            
            'email' => [
                'nullable', 
                'email', 
                'max:255', 
                \Illuminate\Validation\Rule::unique('clients')->where(function ($query) {
                    return $query->where('company_id', $this->company_id);
                })->ignore($clientId),
            ],
            
            'phone' => ['required', 'string', 'max:255'],
            'alternate_phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            
            'tax_number' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            
            'logo_file' => ['nullable', 'image', 'max:2048'],
            'status' => ['nullable', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
