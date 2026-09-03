@props([
    'label' => null,
    'name',
    'options' => [],
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'selected' => null,
    'hint' => null,
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
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
                {!! $icon !!}
            </div>
        @endif

        <select
            name="{{ $name }}"
            id="{{ $name }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->except('class')->merge([
                'class' => 'block w-full rounded-xl text-sm transition-all duration-200 border appearance-none ' .
                    ($errors->has($name) 
                        ? 'border-rose-300 bg-rose-50/30 text-rose-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10' 
                        : 'border-gray-200 bg-gray-50/50 hover:bg-white hover:border-gray-300 text-gray-900 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10') .
                    ' ' . ($icon ? 'pl-10' : 'pl-3.5') .
                    ' pr-10 py-2.5 min-h-[42px] disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed shadow-xs cursor-pointer'
            ]) }}
        >
            @if($placeholder !== null)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach($options as $value => $text)
                <option value="{{ $value }}" @selected((string) old($name, $selected) === (string) $value)>
                    {{ $text }}
                </option>
            @endforeach

            {{ $slot }}
        </select>

        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
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
