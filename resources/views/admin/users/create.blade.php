<x-admin.layout.app title="Thêm quản trị viên" active="users">
	{{-- Breadcrumb --}}
	<div class="mb-4 flex items-center gap-2 text-sm">
		<a href="{{ route('admin.users.index') }}" class="text-text-secondary hover:text-brand transition-colors cursor-pointer">Quản trị viên</a>
		<svg class="w-3.5 h-3.5 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
		<span class="text-text-primary font-medium">Thêm mới</span>
	</div>

	<x-admin.layout.form-panel>
		<form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
			@csrf
			<div>
				<label for="name" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Họ tên</label>
				<input id="name" name="name" value="{{ old('name') }}" placeholder="Nguyễn Văn A" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
			</div>
			<div>
				<label for="email" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Email</label>
				<input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@ieltstl.vn" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
			</div>
			<div>
				<label for="password" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Mật khẩu</label>
				<input id="password" name="password" type="password" placeholder="Tối thiểu 8 ký tự" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
			</div>
			<div>
				<label for="password_confirmation" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Xác nhận mật khẩu</label>
				<input id="password_confirmation" name="password_confirmation" type="password" placeholder="Nhập lại mật khẩu" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
			</div>
			<div class="flex items-center gap-3 pt-2 border-t border-border-light">
				<button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
					Lưu
				</button>
				<a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 border border-border-light rounded-lg text-sm text-text-secondary hover:bg-gray-50 transition-colors cursor-pointer">Hủy</a>
			</div>
		</form>
	</x-admin.layout.form-panel>
</x-admin.layout.app>
