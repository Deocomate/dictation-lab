<x-admin.layout.app title="Thêm gói cước" active="plans">
	{{-- Breadcrumb --}}
	<div class="mb-4 flex items-center gap-2 text-sm">
		<a href="{{ route('admin.plans.index') }}" class="text-text-secondary hover:text-brand transition-colors cursor-pointer">Gói cước</a>
		<svg class="w-3.5 h-3.5 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
		<span class="text-text-primary font-medium">Thêm mới</span>
	</div>

	<x-admin.layout.form-panel>
		@include('admin.plans.partials.form', [
			'action' => route('admin.plans.store'),
			'method' => 'POST',
			'plan' => null,
		])
	</x-admin.layout.form-panel>
</x-admin.layout.app>
