@php use App\Helpers\FormatHelper; @endphp

<x-admin.layout.app title="Quản lý gói cước" active="plans">
	{{-- Header --}}
	<div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
		<p class="text-sm text-text-secondary">Quản lý các gói cước và giá dịch vụ.</p>
		<a href="{{ route('admin.plans.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-brand text-white text-sm font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
			<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
			Thêm gói cước
		</a>
	</div>

	{{-- Table --}}
	<div class="bg-white border border-border-light rounded-xl overflow-hidden shadow-card">
		<div class="overflow-x-auto">
			<table class="min-w-full text-sm table-hover">
				<thead>
					<tr class="bg-gray-50/80 border-b border-border-light">
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Tên gói</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Thời hạn</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Giá</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Trạng thái</th>
						<th class="px-5 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wider">Thao tác</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-border-light">
					@forelse($plans as $plan)
						<tr>
							<td class="px-5 py-3.5">
								<div class="flex items-center gap-2.5">
									<div class="w-8 h-8 rounded-lg bg-brand-light flex items-center justify-center flex-shrink-0">
										<svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
									</div>
									<p class="font-medium text-text-primary">{{ $plan->name }}</p>
								</div>
							</td>
							<td class="px-5 py-3.5 text-text-secondary">{{ $plan->duration_days }} ngày</td>
							<td class="px-5 py-3.5 font-semibold text-text-primary">{{ FormatHelper::money($plan->price) }}</td>
							<td class="px-5 py-3.5">
								@if($plan->is_active)
									<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700">
										<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
										Active
									</span>
								@else
									<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-text-secondary">
										<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
										Inactive
									</span>
								@endif
							</td>
							<td class="px-5 py-3.5 text-right">
								<div class="flex justify-end gap-1.5">
									<a href="{{ route('admin.plans.edit', $plan) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-text-secondary hover:text-blue-600 hover:bg-blue-50 border border-border-light transition-all cursor-pointer">
										<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
										Sửa
									</a>
									<form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" onsubmit="return confirm('Bạn có chắc muốn xóa gói cước này?')">
										@csrf
										@method('DELETE')
										<button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-red-600 hover:bg-red-50 border border-border-light transition-all cursor-pointer">
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
								<p class="text-sm text-text-secondary">Chưa có gói cước nào.</p>
								<a href="{{ route('admin.plans.create') }}" class="mt-2 inline-block text-sm text-brand hover:underline">Tạo gói cước đầu tiên &rarr;</a>
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

	<div class="mt-4">{{ $plans->links() }}</div>
</x-admin.layout.app>
