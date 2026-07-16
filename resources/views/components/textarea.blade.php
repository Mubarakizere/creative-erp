@props([
    'label' => null,
    'name',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'rows' => 4,
    'hint' => null,
    'value' => null,
])

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->except('class')->merge([
                'class' => 'block w-full rounded-lg border ' .
                    ($errors->has($name) ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500') .
                    ' shadow-sm text-sm transition-colors duration-200 ' .
                    ' px-3 py-2.5 disabled:bg-gray-50 disabled:text-gray-500'
            ]) }}
        >{{ old($name, $value) }}</textarea>
    </div>

    @error($name)
        <p class="text-sm text-red-600 flex items-center gap-1">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </p>
    @enderror

    @if($hint && !$errors->has($name))
        <p class="text-sm text-gray-500">{{ $hint }}</p>
    @endif
</div>
