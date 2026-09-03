@props([
    'label' => null,
    'name',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'rows' => 4,
    'hint' => null,
    'value' => null,
    'icon' => null,
])

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold uppercase tracking-wider text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-rose-500 font-bold ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative group">
        @if($icon)
            <div class="absolute top-3 left-0 pl-3.5 flex items-start pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
                {!! $icon !!}
            </div>
        @endif

        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            {{ $attributes->except('class')->merge([
                'class' => 'block w-full rounded-xl text-sm transition-all duration-200 border ' .
                    ($errors->has($name) 
                        ? 'border-rose-300 bg-rose-50/30 text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10' 
                        : 'border-gray-200 bg-gray-50/50 hover:bg-white hover:border-gray-300 text-gray-900 placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10') .
                    ' ' . ($icon ? 'pl-10' : 'pl-3.5') .
                    ' pr-3.5 py-2.5 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed shadow-xs'
            ]) }}
        >{{ old($name, $value) }}</textarea>
    </div>

    @error($name)
        <p class="text-xs text-rose-600 font-medium flex items-center gap-1 mt-1">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </p>
    @enderror

    @if($hint && !$errors->has($name))
        <p class="text-xs text-gray-500 mt-1">{{ $hint }}</p>
    @endif
</div>
