@props([
    'theme' => 'dark', // 'dark' (sidebar) or 'light' (auth/portal)
    'size' => 'md',    // 'md' or 'lg'
    'withLink' => true,
    'href' => null,
    'showBadge' => true,
    'badgeText' => 'ADMIN',
    'class' => '',
])

@php
    $href = $href ?? route('home');

    $isDark = $theme === 'dark';

    $markSizes = [
        'md' => 'w-8 h-8 rounded-xl',
        'lg' => 'w-11 h-11 rounded-2xl',
    ];

    $barContainers = [
        'md' => 'h-3.5 gap-[2.5px]',
        'lg' => 'h-5 gap-[3px]',
    ];

    $barStyles = [
        'md' => [
            'w-[2.5px] h-2',
            'w-[2.5px] h-3.5',
            'w-[2.5px] h-2.5',
            'w-[2.5px] h-1.5',
        ],
        'lg' => [
            'w-[3px] h-3',
            'w-[3px] h-5',
            'w-[3px] h-4',
            'w-[3px] h-2',
        ],
    ];

    $textSizes = [
        'md' => 'text-base',
        'lg' => 'text-2xl sm:text-3xl',
    ];

    $dotSizes = [
        'md' => 'w-1.5 h-1.5 ml-1.5',
        'lg' => 'w-2 h-2 ml-2',
    ];

    $badgeSizes = [
        'md' => 'text-[10px] px-1.5 py-0.5 ml-2',
        'lg' => 'text-xs px-2.5 py-0.5 ml-2.5',
    ];

    $selectedMarkSize = $markSizes[$size] ?? $markSizes['md'];
    $selectedBarContainer = $barContainers[$size] ?? $barContainers['md'];
    $selectedBars = $barStyles[$size] ?? $barStyles['md'];
    $selectedTextSize = $textSizes[$size] ?? $textSizes['md'];
    $selectedDotSize = $dotSizes[$size] ?? $dotSizes['md'];
    $selectedBadgeSize = $badgeSizes[$size] ?? $badgeSizes['md'];

    $markClasses = $isDark
        ? 'bg-slate-800/90 border border-slate-700/80 text-white shadow-inner group-hover:border-emerald-500/40 group-hover:bg-slate-800'
        : 'bg-slate-950 text-white shadow-sm ring-1 ring-black/5 group-hover:bg-brand group-hover:scale-105';

    $dictationColor = $isDark
        ? 'text-white group-hover:text-slate-100'
        : 'text-text-primary group-hover:text-slate-950';

    $labColor = $isDark ? 'text-emerald-400' : 'text-brand';
    $dotColor = $isDark ? 'bg-emerald-400' : 'bg-brand';

    $badgeClasses = $isDark
        ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
        : 'bg-emerald-50 text-brand border-emerald-200/80';
@endphp

@if ($withLink)
<a href="{{ $href }}"
   target="_blank"
   rel="noopener noreferrer"
   class="inline-flex items-center gap-2.5 cursor-pointer group select-none transition-colors {{ $class }}"
   title="Mở trang chủ Dictation Lab trong tab mới"
   aria-label="Dictation Lab Admin">
@else
<div class="inline-flex items-center gap-2.5 select-none {{ $class }}">
@endif

    {{-- Soundwave Monogram Mark --}}
    <div class="relative flex items-center justify-center shrink-0 {{ $selectedMarkSize }} {{ $markClasses }} transition-all duration-200">
        <div class="flex items-center {{ $selectedBarContainer }}">
            <span class="{{ $selectedBars[0] }} bg-emerald-400 rounded-full group-hover:bg-white transition-colors duration-200"></span>
            <span class="{{ $selectedBars[1] }} bg-emerald-400 rounded-full group-hover:bg-white transition-colors duration-200"></span>
            <span class="{{ $selectedBars[2] }} bg-emerald-400 rounded-full group-hover:bg-white transition-colors duration-200"></span>
            <span class="{{ $selectedBars[3] }} bg-emerald-400 rounded-full group-hover:bg-white transition-colors duration-200"></span>
        </div>
    </div>

    {{-- Typographic Wordmark & Admin Tag --}}
    <div class="flex items-center shrink-0 {{ $selectedTextSize }} font-brand leading-none">
        <span class="font-extrabold tracking-[-0.03em] {{ $dictationColor }} transition-colors">
            Dictation
        </span>
        <span class="font-bold tracking-[-0.01em] {{ $labColor }} ml-1">
            Lab
        </span>
        <span class="inline-block {{ $selectedDotSize }} rounded-full {{ $dotColor }} animate-pulse shrink-0" aria-hidden="true"></span>

        @if ($showBadge)
            <span class="inline-flex items-center font-mono font-bold uppercase tracking-wider border rounded {{ $badgeClasses }} {{ $selectedBadgeSize }} shrink-0">
                {{ $badgeText }}
            </span>
        @endif
    </div>

@if ($withLink)
</a>
@else
</div>
@endif
