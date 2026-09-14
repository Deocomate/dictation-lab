@props([
    'name',
    'id' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'class' => '',
    'icon' => null,
])

@php
    $id = $id ?? $name;
    $hasError = !empty($error) || $errors->has($name);
    $baseValue = old($name, $value ?? '');
@endphp

<div class="relative w-full">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-text-disabled">
            {!! $icon !!}
        </div>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $baseValue }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        {{ $attributes->merge([
            'class' => 'w-full bg-white border rounded-xl px-3.5 py-2.5 text-sm text-text-primary placeholder:text-text-disabled focus:outline-none transition-all shadow-xs ' .
            ($icon ? 'pl-10 ' : '') .
            ($hasError
                ? 'border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-100 text-red-900 '
                : 'border-border-light hover:border-gray-300 focus:border-brand focus:ring-2 focus:ring-brand/20 ') .
            ($disabled ? 'bg-gray-50 opacity-60 cursor-not-allowed ' : '') .
            $class
        ]) }}
    />
</div>
