@props([
    'name',
    'id' => null,
    'value' => null,
    'options' => [],
    'placeholder' => 'Chọn...',
    'searchable' => false,
    'autoSubmit' => false,
    'size' => 'md', // 'sm' | 'md'
    'required' => false,
    'disabled' => false,
    'class' => '',
])

@php
    $id = $id ?? $name;
    $currentVal = (string) old($name, $value ?? '');

    // Normalize options into array of objects with value, label, dot, badge
    $normalizedOptions = collect($options)->map(function ($item, $key) {
        if (is_array($item)) {
            $val = (string) ($item['value'] ?? $key);
            $lbl = (string) ($item['label'] ?? $item['name'] ?? $val);
            return [
                'value' => $val,
                'label' => $lbl,
                'dot' => $item['dot'] ?? null,
                'badge' => $item['badge'] ?? null,
            ];
        }
        if (is_object($item)) {
            $val = (string) ($item->id ?? $item->value ?? $key);
            $lbl = (string) ($item->name ?? $item->label ?? $val);
            return [
                'value' => $val,
                'label' => $lbl,
                'dot' => $item->dot ?? null,
                'badge' => $item->badge ?? null,
            ];
        }
        return [
            'value' => (string) $key,
            'label' => (string) $item,
            'dot' => null,
            'badge' => null,
        ];
    })->values()->all();

    // Find initial selected label
    $initialLabel = $placeholder;
    foreach ($normalizedOptions as $opt) {
        if ((string) $opt['value'] === $currentVal) {
            $initialLabel = $opt['label'];
            break;
        }
    }

    $sizeClasses = match($size) {
        'sm' => 'px-2.5 py-1.5 text-xs rounded-lg',
        default => 'px-3.5 py-2.5 text-sm rounded-xl',
    };
@endphp

<div
    x-data="{
        open: false,
        selectedValue: @js($currentVal),
        options: @js($normalizedOptions),
        search: '',
        autoSubmit: @js($autoSubmit),
        get filteredOptions() {
            if (!this.search) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },
        get currentOption() {
            return this.options.find(o => String(o.value) === String(this.selectedValue));
        },
        get currentLabel() {
            const opt = this.currentOption;
            return opt ? opt.label : @js($placeholder);
        },
        select(val) {
            if (this.selectedValue !== String(val)) {
                this.selectedValue = String(val);
                this.$nextTick(() => {
                    this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    if (this.autoSubmit) {
                        const form = this.$el.closest('form');
                        if (form) form.submit();
                    }
                });
            }
            this.open = false;
            this.search = '';
        },
        getDotClass(opt) {
            if (opt.dot) return opt.dot;
            const l = (opt.label || '').toLowerCase();
            const v = String(opt.value).toLowerCase();
            if (l.includes('active') || l.includes('published') || l.includes('success') || v === 'active' || v === 'published' || v === 'success') {
                return 'bg-emerald-500';
            }
            if (l.includes('draft') || l.includes('pending') || v === 'draft' || v === 'pending') {
                return 'bg-amber-500';
            }
            if (l.includes('inactive') || l.includes('locked') || l.includes('failed') || v === 'inactive' || v === 'locked' || v === 'failed') {
                return 'bg-red-500';
            }
            return '';
        }
    }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="relative inline-block text-left w-full {{ $class }}"
>
    {{-- Hidden real input for standard form submission --}}
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $id }}"
        :value="selectedValue"
        x-ref="hiddenInput"
        @if($required) required @endif
    />

    {{-- Trigger Button --}}
    <button
        type="button"
        @click="open = !open; if(open && $refs.searchInput) $nextTick(() => $refs.searchInput.focus())"
        :aria-expanded="open"
        class="w-full flex items-center justify-between gap-2 bg-white border border-border-light text-text-primary shadow-xs hover:border-brand/60 focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand transition-all cursor-pointer select-none {{ $sizeClasses }} @if($disabled) opacity-50 cursor-not-allowed @endif"
        @if($disabled) disabled @endif
    >
        <div class="flex items-center gap-2 truncate min-w-0">
            <template x-if="currentOption && getDotClass(currentOption)">
                <span class="w-2 h-2 rounded-full shrink-0" :class="getDotClass(currentOption)"></span>
            </template>
            <span
                class="truncate font-medium"
                :class="!currentOption && !selectedValue ? 'text-text-disabled font-normal' : 'text-text-primary'"
                x-text="currentLabel"
            ></span>
        </div>

        <div class="flex items-center gap-1.5 shrink-0 text-text-secondary ml-1">
            <svg
                class="w-4 h-4 transition-transform duration-200"
                :class="open ? 'rotate-180 text-brand' : ''"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>

    {{-- Dropdown Menu --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-98"
        x-cloak
        class="absolute left-0 z-50 mt-1.5 w-full min-w-[12rem] bg-white border border-border-light rounded-xl shadow-float p-1.5 max-h-64 overflow-y-auto space-y-0.5 focus:outline-none"
    >
        @if($searchable)
            <div class="sticky top-0 bg-white z-10 pb-1.5 pt-0.5 border-b border-border-light mb-1 px-1">
                <div class="relative">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input
                        x-ref="searchInput"
                        x-model="search"
                        type="text"
                        placeholder="Tìm kiếm..."
                        class="w-full pl-8 pr-2.5 py-1.5 text-xs bg-gray-50 border border-border-light rounded-lg focus:outline-none focus:bg-white focus:border-brand focus:ring-1 focus:ring-brand/20 transition-all"
                        @click.stop
                    />
                </div>
            </div>
        @endif

        <template x-for="opt in filteredOptions" :key="String(opt.value)">
            <button
                type="button"
                @click="select(opt.value)"
                class="w-full flex items-center justify-between gap-2 px-2.5 py-2 text-xs sm:text-sm rounded-lg transition-colors cursor-pointer text-left"
                :class="String(selectedValue) === String(opt.value) ? 'bg-brand-light/70 text-brand-dark font-semibold' : 'text-text-primary hover:bg-gray-50 hover:text-text-primary'"
            >
                <div class="flex items-center gap-2 truncate min-w-0">
                    <template x-if="getDotClass(opt)">
                        <span class="w-2 h-2 rounded-full shrink-0" :class="getDotClass(opt)"></span>
                    </template>
                    <span class="truncate" x-text="opt.label"></span>
                    <template x-if="opt.badge">
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded uppercase tracking-wider bg-purple-100 text-purple-700" x-text="opt.badge"></span>
                    </template>
                </div>

                <template x-if="String(selectedValue) === String(opt.value)">
                    <svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </template>
            </button>
        </template>

        <div x-show="filteredOptions.length === 0" class="py-3 px-2 text-center text-xs text-text-secondary">
            Không tìm thấy lựa chọn nào.
        </div>
    </div>
</div>
