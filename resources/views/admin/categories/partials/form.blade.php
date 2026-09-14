<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    {{-- Thông tin cơ bản --}}
    <fieldset class="space-y-4">
        <legend class="text-xs font-bold text-text-secondary uppercase tracking-wider flex items-center gap-2 mb-1">
            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Thông tin danh mục
        </legend>

        {{-- Name --}}
        <x-admin.form.field label="Tên danh mục" for="name" required :error="$errors->first('name')">
            <x-admin.form.input id="name" name="name" :value="old('name', $category?->name)" placeholder="VD: Khoa học, Công nghệ, Văn hóa..." required />
        </x-admin.form.field>

        {{-- Slug --}}
        <x-admin.form.field label="Slug" for="slug" hint="(tùy chọn — tự động tạo nếu để trống)" :error="$errors->first('slug')">
            <x-admin.form.input id="slug" name="slug" :value="old('slug', $category?->slug)" placeholder="vd: khoa-hoc" class="font-mono text-xs" />
        </x-admin.form.field>

        {{-- Description --}}
        <x-admin.form.field label="Mô tả" for="description" hint="(tùy chọn)" :error="$errors->first('description')">
            <x-admin.form.textarea id="description" name="description" :value="old('description', $category?->description)" rows="3" placeholder="Mô tả ngắn về danh mục này..." />
        </x-admin.form.field>
    </fieldset>

    <hr class="border-border-light" />

    {{-- Trạng thái --}}
    <fieldset class="space-y-4">
        <legend class="text-xs font-bold text-text-secondary uppercase tracking-wider flex items-center gap-2 mb-1">
            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Phát hành
        </legend>

        <x-admin.form.field label="Trạng thái hiển thị" for="status" required :error="$errors->first('status')">
            <x-admin.form.select
                id="status"
                name="status"
                :value="old('status', $category?->status ?? 'active')"
                :options="[
                    ['value' => 'active', 'label' => 'Active — Hiển thị trên hệ thống', 'dot' => 'bg-emerald-500'],
                    ['value' => 'inactive', 'label' => 'Inactive — Tạm ẩn danh mục', 'dot' => 'bg-gray-400'],
                ]"
                required
            />
        </x-admin.form.field>
    </fieldset>

    {{-- Actions --}}
    <div class="flex items-center gap-3 pt-4 border-t border-border-light">
        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Lưu danh mục
        </button>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2.5 border border-border-light rounded-lg text-sm text-text-secondary hover:bg-gray-50 transition-colors cursor-pointer">Hủy</a>
    </div>
</form>
