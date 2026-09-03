<x-admin.layout.app title="Thêm bài viết" active="articles">
    {{-- Breadcrumb --}}
    <div class="mb-4 flex items-center gap-2 text-sm">
        <a href="{{ route('admin.articles.index') }}" class="text-text-secondary hover:text-brand transition-colors cursor-pointer">Bài viết</a>
        <svg class="w-3.5 h-3.5 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-text-primary font-medium">Thêm mới</span>
    </div>

    <x-admin.layout.form-panel>
        @include('admin.articles.partials.form', [
            'action' => route('admin.articles.store'),
            'method' => 'POST',
            'article' => null,
        ])
    </x-admin.layout.form-panel>
</x-admin.layout.app>
