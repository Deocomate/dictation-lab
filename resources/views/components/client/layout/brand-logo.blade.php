@props([
    'size' => 'md', // 'sm', 'md', 'lg'
    'withLink' => true,
    'href' => null,
    'variant' => 'default', // 'default', 'subtle'
    'showTag' => false,
    'tagText' => 'AI',
    'class' => '',
])

@php
    $href = $href ?? route('home');

    // Sizing maps
    $markSizes = [
        'sm' => 'w-6 h-6 rounded-lg',
        'md' => 'w-8 h-8 rounded-xl',
        'lg' => 'w-10 h-10 rounded-2xl',
    ];

    $barContainerHeights = [
        'sm' => 'h-2.5 gap-[2px]',
        'md' => 'h-3.5 gap-[2.5px]',
        'lg' => 'h-4.5 gap-[3px]',
    ];

    $barStyles = [
        'sm' => [
            'w-[2px] h-1.5',
            'w-[2px] h-2.5',
            'w-[2px] h-2',
            'w-[2px] h-1',
        ],
        'md' => [
            'w-[2.5px] h-2',
            'w-[2.5px] h-3.5',
            'w-[2.5px] h-2.5',
            'w-[2.5px] h-1.5',
        ],
        'lg' => [
            'w-[3px] h-2.5',
            'w-[3px] h-4.5',
            'w-[3px] h-3.5',
            'w-[3px] h-2',
        ],
    ];

    $textSizes = [
        'sm' => 'text-sm',
        'md' => 'text-base sm:text-[1.125rem]',
        'lg' => 'text-xl sm:text-2xl',
    ];

    $dotSizes = [
        'sm' => 'w-1 h-1 ml-0.5',
        'md' => 'w-1.5 h-1.5 ml-1',
        'lg' => 'w-2 h-2 ml-1.5',
    ];

    $tagSizes = [
        'sm' => 'text-[9px] px-1 py-0.2 ml-1.5',
        'md' => 'text-[10px] px-1.5 py-0.5 ml-2',
        'lg' => 'text-xs px-2 py-0.5 ml-2.5',
    ];

    $selectedMarkSize = $markSizes[$size] ?? $markSizes['md'];
    $selectedBarContainer = $barContainerHeights[$size] ?? $barContainerHeights['md'];
    $selectedBars = $barStyles[$size] ?? $barStyles['md'];
    $selectedTextSize = $textSizes[$size] ?? $textSizes['md'];
    $selectedDotSize = $dotSizes[$size] ?? $dotSizes['md'];
    $selectedTagSize = $tagSizes[$size] ?? $tagSizes['md'];

    $markBg = $variant === 'subtle'
        ? 'bg-slate-800 text-white group-hover:bg-brand'
        : 'bg-slate-950 text-white shadow-sm ring-1 ring-black/5 group-hover:bg-brand group-hover:scale-105';
@endphp

@if ($withLink)
<a href="{{ $href }}"
   class="inline-flex items-center gap-2.5 cursor-pointer group select-none {{ $class }}"
   aria-label="Dictation Lab">
@else
<div class="inline-flex items-center gap-2.5 select-none {{ $class }}">
@endif

    {{-- Minimalist Soundwave Monogram Mark --}}
    <div class="relative flex items-center justify-center shrink-0 {{ $selectedMarkSize }} {{ $markBg }} transition-all duration-200">
        <div class="flex items-center {{ $selectedBarContainer }}">
            <span class="{{ $selectedBars[0] }} bg-emerald-400 rounded-full group-hover:bg-white transition-colors duration-200"></span>
            <span class="{{ $selectedBars[1] }} bg-emerald-400 rounded-full group-hover:bg-white transition-colors duration-200"></span>
            <span class="{{ $selectedBars[2] }} bg-emerald-400 rounded-full group-hover:bg-white transition-colors duration-200"></span>
            <span class="{{ $selectedBars[3] }} bg-emerald-400 rounded-full group-hover:bg-white transition-colors duration-200"></span>
        </div>
    </div>

    {{-- Typographic Wordmark --}}
    <div class="flex items-baseline {{ $selectedTextSize }} font-brand leading-none">
        <span class="font-extrabold tracking-[-0.035em] text-text-primary group-hover:text-slate-950 transition-colors">
            Dictation
        </span>
        <span class="font-bold tracking-[-0.02em] text-brand ml-0.5">
            Lab
        </span>
        <span class="inline-block {{ $selectedDotSize }} rounded-full bg-brand animate-pulse" aria-hidden="true"></span>

        @if ($showTag)
            <span class="inline-flex items-center font-mono font-bold uppercase tracking-wider bg-brand-light text-brand border border-brand/20 rounded {{ $selectedTagSize }}">
                {{ $tagText }}
            </span>
        @endif
    </div>

@if ($withLink)
</a>
@else
</div>
@endif
