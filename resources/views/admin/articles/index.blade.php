<x-admin.layout.app title="Quản lý bài viết" active="articles">
    {{-- Page header --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        @php
            $categoryOptions = collect([['value' => '', 'label' => 'Tất cả danh mục']])
                ->merge($categories->map(fn($c) => ['value' => (string) $c->id, 'label' => $c->name]))
                ->all();
            $accessOptions = [
                ['value' => '', 'label' => 'Tất cả loại bài'],
                ['value' => 'free', 'label' => 'Free — Miễn phí', 'badge' => 'Free'],
                ['value' => 'pro', 'label' => 'Pro — Trả phí', 'badge' => 'Pro'],
            ];
            $statusOptions = [
                ['value' => '', 'label' => 'Tất cả trạng thái'],
                ['value' => 'draft', 'label' => 'Draft — Bản nháp', 'dot' => 'bg-amber-500'],
                ['value' => 'published', 'label' => 'Published — Đã xuất bản', 'dot' => 'bg-emerald-500'],
            ];
        @endphp

        <form method="GET" class="flex flex-wrap items-center gap-2.5">
            <div class="w-56">
                <x-admin.form.input
                    name="search"
                    :value="$filters['search'] ?? ''"
                    placeholder="Tìm bài viết..."
                    icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>'
                />
            </div>

            <div class="w-48">
                <x-admin.form.select
                    name="category_id"
                    :value="$filters['category_id'] ?? ''"
                    :options="$categoryOptions"
                    placeholder="Tất cả danh mục"
                    :searchable="true"
                    :auto-submit="true"
                />
            </div>

            <div class="w-40">
                <x-admin.form.select
                    name="access"
                    :value="$filters['access'] ?? ''"
                    :options="$accessOptions"
                    placeholder="Loại truy cập"
                    :auto-submit="true"
                />
            </div>

            <div class="w-44">
                <x-admin.form.select
                    name="status"
                    :value="$filters['status'] ?? ''"
                    :options="$statusOptions"
                    placeholder="Tất cả trạng thái"
                    :auto-submit="true"
                />
            </div>

            <button type="submit" class="px-3.5 py-2.5 text-sm font-medium bg-gray-100 hover:bg-gray-200 text-text-primary rounded-xl transition-colors cursor-pointer shadow-2xs">Lọc</button>
            @if(!empty($filters['search']) || !empty($filters['category_id']) || !empty($filters['access']) || !empty($filters['status']))
                <a href="{{ route('admin.articles.index') }}" class="px-3 py-2.5 text-xs text-text-secondary hover:text-red-600 transition-colors">Xóa lọc</a>
            @endif
        </form>
        <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-brand text-white text-sm font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Thêm bài viết
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-border-light rounded-xl overflow-hidden shadow-card">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm table-hover">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-border-light">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Tiêu đề</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Danh mục</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Loại</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Trạng thái</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-light">
                    @forelse($articles as $article)
                        <tr>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if($article->image_path)
                                        <img src="{{ $article->imageUrl() }}" class="w-12 h-10 object-cover rounded border border-border-light shrink-0" alt="Thumbnail">
                                    @else
                                        <div class="w-12 h-10 bg-gray-100 rounded border border-dashed border-gray-300 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-medium text-text-primary truncate max-w-xs">{{ $article->title }}</p>
                                        @if($article->excerpt)
                                            <p class="text-xs text-text-secondary mt-0.5 truncate max-w-xs">{{ $article->excerpt }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($article->categories as $cat)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand-light text-brand">{{ $cat->name }}</span>
                                    @empty
                                        <span class="text-text-disabled text-xs">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($article->is_premium)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-yellow-50 text-yellow-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Pro
                                    </span>
                                @else
                                    <span class="text-xs text-text-secondary">Free</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($article->status === 'published')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-text-disabled">
                                        <span class="w-1.5 h-1.5 rounded-full bg-text-disabled"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-text-secondary hover:text-blue-600 hover:bg-blue-50 border border-border-light transition-all cursor-pointer" title="Sửa">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Sửa
                                    </a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Xác nhận xóa bài viết này?')">
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
                                <svg class="w-10 h-10 text-text-disabled mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <p class="text-sm text-text-secondary">Chưa có bài viết nào.</p>
                                <a href="{{ route('admin.articles.create') }}" class="inline-block mt-2 text-sm text-brand hover:text-brand-dark font-medium cursor-pointer">Thêm bài viết đầu tiên</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $articles->withQueryString()->links() }}</div>
</x-admin.layout.app>
