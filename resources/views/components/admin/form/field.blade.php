@props([
    'label' => null,
    'for' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'class' => '',
])

<div class="space-y-1.5 {{ $class }}">
    @if($label)
        <div class="flex items-center justify-between gap-2">
            <label @if($for) for="{{ $for }}" @endif class="block text-xs font-semibold text-text-secondary uppercase tracking-wider">
                {{ $label }}
                @if($required)
                    <span class="text-red-500 font-bold ml-0.5">*</span>
                @endif
            </label>
            @if($hint)
                <span class="text-[11px] text-text-disabled">{{ $hint }}</span>
            @endif
        </div>
    @endif

    {{ $slot }}

    @if($error)
        <p class="text-xs text-red-500 flex items-center gap-1 mt-1 font-medium">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
