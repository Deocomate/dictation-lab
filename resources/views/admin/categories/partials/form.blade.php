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
        <div>
            <label for="name" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Tên danh mục <span class="text-red-500">*</span></label>
            <input id="name" name="name" value="{{ old('name', $category?->name) }}" placeholder="VD: Khoa học, Công nghệ, Văn hóa..." class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Slug --}}
        <div>
            <label for="slug" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Slug <span class="font-normal text-text-disabled">(tùy chọn — tự tạo nếu để trống)</span></label>
            <input id="slug" name="slug" value="{{ old('slug', $category?->slug) }}" placeholder="vd: khoa-hoc" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all font-mono" />
            @error('slug')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Mô tả <span class="font-normal text-text-disabled">(tùy chọn)</span></label>
            <textarea id="description" name="description" rows="3" placeholder="Mô tả ngắn về danh mục này..." class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all leading-relaxed">{{ old('description', $category?->description) }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
    </fieldset>

    <hr class="border-border-light" />

    {{-- Trạng thái --}}
    <fieldset class="space-y-4">
        <legend class="text-xs font-bold text-text-secondary uppercase tracking-wider flex items-center gap-2 mb-1">
            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Phát hành
        </legend>

        <div>
            <label for="status" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Trạng thái</label>
            <select id="status" name="status" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm cursor-pointer transition-all" required>
                <option value="active" @selected(old('status', $category?->status ?? 'active') === 'active')>Active — Hiển thị</option>
                <option value="inactive" @selected(old('status', $category?->status) === 'inactive')>Inactive — Ẩn</option>
            </select>
            @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
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
