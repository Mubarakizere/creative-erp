<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * The supported settings whitelist.
     * Maps the setting key to its expected group and type.
     */
    public const SUPPORTED_SETTINGS = [
        'system_name' => ['group' => 'general', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
        'date_format' => ['group' => 'general', 'type' => 'text', 'rules' => 'nullable|string|max:50'],
        'time_format' => ['group' => 'general', 'type' => 'text', 'rules' => 'nullable|string|max:50'],
        'maintenance_mode' => ['group' => 'general', 'type' => 'boolean', 'rules' => 'nullable|boolean'],
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('settings.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'settings' => 'required|array',
        ];

        foreach (self::SUPPORTED_SETTINGS as $key => $config) {
            $rules['settings.' . $key] = $config['rules'];
        }

        return $rules;
    }
}
