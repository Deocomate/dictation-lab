<x-admin.layout.app title="Quản lý danh mục" active="categories">
    {{-- Page header --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm danh mục..." class="input-admin pl-9 pr-3 py-2 text-sm border border-border-light rounded-lg w-52 transition-all" />
            </div>
            <select name="status" class="input-admin border border-border-light rounded-lg px-3 py-2 text-sm cursor-pointer" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
            </select>
            <button type="submit" class="px-3 py-2 text-sm bg-gray-100 text-text-primary rounded-lg hover:bg-gray-200 transition-colors cursor-pointer">Lọc</button>
        </form>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-brand text-white text-sm font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Thêm danh mục
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-border-light rounded-xl overflow-hidden shadow-card">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm table-hover">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-border-light">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Tên danh mục</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Slug</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Trạng thái</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Số bài viết</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-light">
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-text-primary">{{ $category->name }}</p>
                                @if($category->description)
                                    <p class="text-xs text-text-secondary mt-0.5 truncate max-w-xs">{{ $category->description }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <code class="text-xs bg-gray-100 text-text-secondary px-2 py-0.5 rounded font-mono">{{ $category->slug }}</code>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($category->status === 'active')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-text-disabled">
                                        <span class="w-1.5 h-1.5 rounded-full bg-text-disabled"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-sm font-semibold text-text-primary">{{ $category->articles_count }}</span>
                                <span class="text-xs text-text-disabled ml-1">bài</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-text-secondary hover:text-blue-600 hover:bg-blue-50 border border-border-light transition-all cursor-pointer" title="Sửa">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Sửa
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Xác nhận xóa danh mục này? Các bài viết thuộc danh mục sẽ không bị xóa.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 border border-red-100 transition-all cursor-pointer" title="Xóa">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <svg class="w-10 h-10 text-text-disabled mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776"/></svg>
                                <p class="text-sm text-text-secondary">Chưa có danh mục nào.</p>
                                <a href="{{ route('admin.categories.create') }}" class="inline-block mt-2 text-sm text-brand hover:text-brand-dark font-medium cursor-pointer">Thêm danh mục đầu tiên</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $categories->withQueryString()->links() }}</div>
</x-admin.layout.app>
