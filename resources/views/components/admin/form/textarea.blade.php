@props([
    'name',
    'id' => null,
    'value' => null,
    'rows' => 3,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'class' => '',
])

@php
    $id = $id ?? $name;
    $hasError = !empty($error) || $errors->has($name);
    $baseValue = old($name, $value ?? '');
@endphp

<textarea
    name="{{ $name }}"
    id="{{ $id }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    @if($required) required @endif
    @if($disabled) disabled @endif
    @if($readonly) readonly @endif
    {{ $attributes->merge([
        'class' => 'w-full bg-white border rounded-xl px-3.5 py-2.5 text-sm text-text-primary placeholder:text-text-disabled focus:outline-none transition-all shadow-xs leading-relaxed ' .
        ($hasError
            ? 'border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-100 text-red-900 '
            : 'border-border-light hover:border-gray-300 focus:border-brand focus:ring-2 focus:ring-brand/20 ') .
        ($disabled ? 'bg-gray-50 opacity-60 cursor-not-allowed ' : '') .
        $class
    ]) }}
>{{ $baseValue }}</textarea>
