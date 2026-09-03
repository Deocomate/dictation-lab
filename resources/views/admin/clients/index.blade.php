<x-admin.layout.app title="Quản lý học viên" active="clients">
	{{-- Header --}}
	<div class="mb-5">
		<p class="text-sm text-text-secondary">Theo dõi và quản lý học viên đang sử dụng hệ thống.</p>
	</div>

	{{-- Filter bar --}}
	<form method="GET" class="mb-5 bg-white border border-border-light rounded-xl p-4 shadow-card">
		<div class="grid md:grid-cols-4 gap-3">
			<div class="relative">
				<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
				<input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm theo tên/email" class="input-admin w-full border border-border-light rounded-lg pl-9 pr-3.5 py-2.5 text-sm transition-all" />
			</div>
			<select name="subscription_tier" class="input-admin border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all">
				<option value="">Tất cả gói</option>
				<option value="free" @selected(($filters['subscription_tier'] ?? '') === 'free')>Free</option>
				<option value="pro" @selected(($filters['subscription_tier'] ?? '') === 'pro')>Pro</option>
			</select>
			<select name="status" class="input-admin border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all">
				<option value="">Tất cả trạng thái</option>
				<option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
				<option value="locked" @selected(($filters['status'] ?? '') === 'locked')>Locked</option>
			</select>
			<button type="submit" class="inline-flex items-center justify-center gap-1.5 bg-brand text-white rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
				Lọc
			</button>
		</div>
	</form>

	{{-- Table --}}
	<div class="bg-white border border-border-light rounded-xl overflow-hidden shadow-card">
		<div class="overflow-x-auto">
			<table class="min-w-full text-sm table-hover">
				<thead>
					<tr class="bg-gray-50/80 border-b border-border-light">
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Học viên</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Email</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Gói</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Trạng thái</th>
						<th class="px-5 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wider">Thao tác</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-border-light">
					@forelse($clients as $client)
						<tr>
							<td class="px-5 py-3.5">
								<div class="flex items-center gap-2.5">
									@php $initial = strtoupper(substr($client->name, 0, 1)); @endphp
									<div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 flex-shrink-0">{{ $initial }}</div>
									<p class="font-medium text-text-primary">{{ $client->name }}</p>
								</div>
							</td>
							<td class="px-5 py-3.5 text-text-secondary">{{ $client->email }}</td>
							<td class="px-5 py-3.5">
								@if($client->subscription_tier === 'pro')
									<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-700">
										<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
										Pro
									</span>
								@else
									<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-text-secondary">Free</span>
								@endif
							</td>
							<td class="px-5 py-3.5">
								@if($client->status === 'active')
									<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700">
										<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
										Active
									</span>
								@else
									<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-600">
										<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
										Locked
									</span>
								@endif
							</td>
							<td class="px-5 py-3.5 text-right">
								<a href="{{ route('admin.clients.show', $client) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-text-secondary hover:text-brand hover:bg-brand-light border border-border-light transition-all cursor-pointer">
									<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
									Chi tiết
								</a>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="5" class="px-5 py-12 text-center">
								<p class="text-sm text-text-secondary">Không tìm thấy học viên nào.</p>
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

	<div class="mt-4">{{ $clients->withQueryString()->links() }}</div>
</x-admin.layout.app>
