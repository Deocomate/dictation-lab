@props([
    'categories' => [],
    'selectedIds' => [],
    'name' => 'category_ids',
    'required' => true,
    'class' => '',
])

@php
    $categoriesList = collect($categories)->map(fn ($cat) => [
        'id' => (int) $cat->id,
        'name' => (string) $cat->name,
        'slug' => (string) $cat->slug,
    ])->values()->all();

    $initialSelected = collect($selectedIds)->map(fn ($id) => (int) $id)->all();
@endphp

<div
    x-data="{
        open: false,
        allCategories: @js($categoriesList),
        selectedIds: @js($initialSelected),
        search: '',
        showQuickAdd: false,
        newCatName: '',
        isSubmitting: false,
        quickAddError: '',
        quickAddSuccess: '',

        get selectedCategories() {
            return this.allCategories.filter(c => this.selectedIds.includes(c.id));
        },
        get filteredCategories() {
            if (!this.search) return this.allCategories;
            const q = this.search.toLowerCase().trim();
            return this.allCategories.filter(c =>
                c.name.toLowerCase().includes(q) || c.slug.toLowerCase().includes(q)
            );
        },
        isSelected(id) {
            return this.selectedIds.includes(Number(id));
        },
        toggle(id) {
            id = Number(id);
            if (this.isSelected(id)) {
                this.selectedIds = this.selectedIds.filter(i => i !== id);
            } else {
                this.selectedIds.push(id);
            }
        },
        remove(id) {
            this.selectedIds = this.selectedIds.filter(i => i !== Number(id));
        },
        openQuickAdd() {
            this.showQuickAdd = true;
            if (this.search && !this.newCatName) {
                this.newCatName = this.search.trim();
            }
            this.quickAddError = '';
            this.$nextTick(() => {
                if (this.$refs.quickAddInput) this.$refs.quickAddInput.focus();
            });
        },
        cancelQuickAdd() {
            this.showQuickAdd = false;
            this.newCatName = '';
            this.quickAddError = '';
        },
        async submitQuickAdd() {
            const name = this.newCatName.trim();
            if (!name) {
                this.quickAddError = 'Vui lòng nhập tên danh mục.';
                return;
            }

            this.isSubmitting = true;
            this.quickAddError = '';

            try {
                const res = await fetch('{{ route('admin.categories.quick-store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ name }),
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Lỗi khi tạo danh mục.'));
                }

                const created = {
                    id: Number(data.category.id),
                    name: data.category.name,
                    slug: data.category.slug,
                };

                // Add to list if not present
                if (!this.allCategories.some(c => c.id === created.id)) {
                    this.allCategories.unshift(created);
                }

                // Auto-select newly created category
                if (!this.selectedIds.includes(created.id)) {
                    this.selectedIds.push(created.id);
                }

                this.quickAddSuccess = `✓ Đã tạo và chọn danh mục '${created.name}'`;
                setTimeout(() => { this.quickAddSuccess = ''; }, 3500);

                this.cancelQuickAdd();
                this.search = '';
            } catch (err) {
                this.quickAddError = err.message || 'Không thể tạo danh mục lúc này.';
            } finally {
                this.isSubmitting = false;
            }
        },
        matchCategories(slugs) {
            if (!Array.isArray(slugs) || slugs.length === 0) return;
            const matchedIds = [];
            slugs.forEach(slug => {
                const cat = this.allCategories.find(c => c.slug === slug || c.name.toLowerCase() === String(slug).toLowerCase());
                if (cat && !matchedIds.includes(cat.id)) {
                    matchedIds.push(cat.id);
                }
            });
            if (matchedIds.length > 0) {
                this.selectedIds = [...new Set([...this.selectedIds, ...matchedIds])];
            }
        }
    }"
    @article-categories-match.window="matchCategories($event.detail.slugs)"
    @click.outside="open = false; showQuickAdd = false"
    @keydown.escape.window="open = false; showQuickAdd = false"
    class="space-y-2.5 {{ $class }}"
>
    {{-- Dynamic Hidden inputs for Form Submission --}}
    <template x-for="id in selectedIds" :key="id">
        <input type="hidden" name="{{ $name }}[]" :value="id" />
    </template>

    {{-- Main Selector Box & Pills --}}
    <div
        class="min-h-[46px] p-2 bg-white border border-border-light rounded-xl flex flex-wrap items-center gap-1.5 focus-within:ring-2 focus-within:ring-brand/20 focus-within:border-brand transition-all shadow-xs cursor-pointer"
        @click="open = true"
    >
        {{-- Selected Tag Badges --}}
        <template x-for="cat in selectedCategories" :key="cat.id">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-brand-light text-brand-dark border border-brand/20 text-xs font-semibold rounded-lg shadow-2xs animate-fadeIn">
                <span x-text="cat.name"></span>
                <button
                    type="button"
                    @click.stop="remove(cat.id)"
                    class="w-3.5 h-3.5 rounded-full inline-flex items-center justify-center hover:bg-brand/20 text-brand-dark transition-colors cursor-pointer"
                    title="Bỏ chọn"
                >
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </span>
        </template>

        {{-- Empty placeholder or add trigger --}}
        <div class="flex-1 flex items-center justify-between min-w-[140px] px-1.5 py-0.5">
            <span
                x-show="selectedCategories.length === 0"
                class="text-sm text-text-disabled select-none"
            >
                Nhấn để chọn danh mục...
            </span>

            <div class="ml-auto flex items-center gap-2 text-text-secondary">
                <template x-if="selectedCategories.length > 0">
                    <span class="text-xs text-text-secondary bg-gray-100 px-2 py-0.5 rounded-md font-medium" x-text="`${selectedCategories.length} đã chọn`"></span>
                </template>
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
        </div>
    </div>

    {{-- Success flash indicator --}}
    <div x-show="quickAddSuccess" x-cloak x-transition class="text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg flex items-center gap-1.5 font-medium">
        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span x-text="quickAddSuccess"></span>
    </div>

    {{-- Dropdown Combobox & Quick-Add Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-98"
        x-cloak
        class="bg-white border border-border-light rounded-xl shadow-float p-3 space-y-3 relative z-40"
    >
        {{-- Search & Quick-Add Button Header --}}
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input
                    type="text"
                    x-model="search"
                    placeholder="Tìm kiếm danh mục..."
                    class="w-full pl-8 pr-3 py-1.5 text-xs sm:text-sm bg-gray-50 border border-border-light rounded-lg focus:outline-none focus:bg-white focus:border-brand focus:ring-1 focus:ring-brand/20 transition-all"
                />
            </div>
            <button
                type="button"
                @click="openQuickAdd()"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-brand-light text-brand-dark hover:bg-brand hover:text-white border border-brand/30 text-xs font-semibold rounded-lg transition-colors cursor-pointer shadow-2xs whitespace-nowrap"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tạo mới</span>
            </button>
        </div>

        {{-- Quick Add In-Place Mini-Form --}}
        <div x-show="showQuickAdd" x-cloak x-transition class="p-3 bg-brand-light/30 border border-brand/20 rounded-xl space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-brand-dark flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tạo nhanh danh mục mới
                </span>
                <button type="button" @click="cancelQuickAdd()" class="text-xs text-text-secondary hover:text-text-primary">Đóng</button>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <input
                    type="text"
                    x-ref="quickAddInput"
                    x-model="newCatName"
                    @keydown.enter.prevent="submitQuickAdd()"
                    placeholder="VD: IELTS Reading, Du học, Công nghệ..."
                    class="flex-1 px-3 py-1.5 text-xs sm:text-sm bg-white border border-border-light rounded-lg focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/20"
                />
                <button
                    type="button"
                    @click="submitQuickAdd()"
                    :disabled="isSubmitting"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-brand text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-brand-dark transition-colors cursor-pointer disabled:opacity-50"
                >
                    <template x-if="isSubmitting">
                        <svg class="animate-spin w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <span x-text="isSubmitting ? 'Đang tạo...' : 'Tạo & Chọn ngay'"></span>
                </button>
            </div>

            <p x-show="quickAddError" x-text="quickAddError" class="text-xs text-red-500 font-medium"></p>
        </div>

        {{-- Categories Grid with Checkboxes --}}
        <div class="max-h-56 overflow-y-auto space-y-1 pr-1">
            <template x-for="cat in filteredCategories" :key="cat.id">
                <label
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-xs sm:text-sm transition-colors cursor-pointer select-none"
                    :class="isSelected(cat.id) ? 'bg-brand-light/60 text-brand-dark font-medium' : 'hover:bg-gray-50 text-text-primary'"
                >
                    <div class="flex items-center gap-2.5 truncate min-w-0">
                        <input
                            type="checkbox"
                            :checked="isSelected(cat.id)"
                            @change="toggle(cat.id)"
                            class="w-4 h-4 text-brand rounded border-border-light focus:ring-brand/20 cursor-pointer"
                        />
                        <span class="truncate" x-text="cat.name"></span>
                    </div>

                    <span class="text-[10px] text-text-disabled font-mono shrink-0" x-text="cat.slug"></span>
                </label>
            </template>

            <div x-show="filteredCategories.length === 0" class="py-6 text-center space-y-2">
                <p class="text-xs text-text-secondary">Không tìm thấy danh mục phù hợp.</p>
                <button
                    type="button"
                    @click="openQuickAdd()"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-brand hover:underline cursor-pointer"
                >
                    + Tạo ngay danh mục "<span x-text="search"></span>"
                </button>
            </div>
        </div>

        {{-- Footer summary --}}
        <div class="pt-2 border-t border-border-light flex items-center justify-between text-xs text-text-secondary">
            <span>Đã chọn <strong class="text-brand font-bold" x-text="selectedIds.length"></strong> danh mục</span>
            <button
                type="button"
                @click="open = false"
                class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-text-primary rounded-md font-medium cursor-pointer transition-colors"
            >
                Xong
            </button>
        </div>
    </div>
</div>
