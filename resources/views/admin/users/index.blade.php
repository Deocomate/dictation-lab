<x-admin.layout.app title="Quản lý quản trị viên" active="users">
	{{-- Header --}}
	<div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
		<p class="text-sm text-text-secondary">Quản lý tài khoản admin và phân quyền hệ thống.</p>
		<a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-brand text-white text-sm font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
			<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
			Thêm admin
		</a>
	</div>

	{{-- Table --}}
	<div class="bg-white border border-border-light rounded-xl overflow-hidden shadow-card">
		<div class="overflow-x-auto">
			<table class="min-w-full text-sm table-hover">
				<thead>
					<tr class="bg-gray-50/80 border-b border-border-light">
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Người dùng</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Email</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Vai trò</th>
						<th class="px-5 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wider">Thao tác</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-border-light">
					@forelse($users as $user)
						<tr>
							<td class="px-5 py-3.5">
								<div class="flex items-center gap-3">
									@php $initial = strtoupper(substr($user->name, 0, 1)); @endphp
									<div class="w-8 h-8 rounded-full {{ $user->isSuperAdmin() ? 'bg-brand' : 'bg-slate-200' }} flex items-center justify-center text-xs font-bold {{ $user->isSuperAdmin() ? 'text-white' : 'text-slate-600' }} flex-shrink-0">{{ $initial }}</div>
									<p class="font-medium text-text-primary">{{ $user->name }}</p>
								</div>
							</td>
							<td class="px-5 py-3.5 text-text-secondary">{{ $user->email }}</td>
							<td class="px-5 py-3.5">
								@if($user->isSuperAdmin())
									<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-brand-light text-brand">
										<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
										Super Admin
									</span>
								@else
									<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-text-secondary">Admin</span>
								@endif
							</td>
							<td class="px-5 py-3.5">
								<div class="flex items-center justify-end gap-1.5">
									<a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-text-secondary hover:text-blue-600 hover:bg-blue-50 border border-border-light transition-all cursor-pointer">
										<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
										Sửa
									</a>
									@if(!$user->isSuperAdmin())
										<form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Xác nhận xóa admin này?')">
											@csrf
											@method('DELETE')
											<button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 border border-red-100 transition-all cursor-pointer">
												<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
												Xóa
											</button>
										</form>
									@endif
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="4" class="px-5 py-12 text-center">
								<p class="text-sm text-text-secondary">Chưa có quản trị viên nào.</p>
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

	<div class="mt-4">{{ $users->links() }}</div>
</x-admin.layout.app>
