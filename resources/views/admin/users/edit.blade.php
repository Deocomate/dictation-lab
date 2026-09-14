<x-admin.layout.app title="Sửa quản trị viên" active="users">
	{{-- Breadcrumb --}}
	<div class="mb-4 flex items-center gap-2 text-sm">
		<a href="{{ route('admin.users.index') }}" class="text-text-secondary hover:text-brand transition-colors cursor-pointer">Quản trị viên</a>
		<svg class="w-3.5 h-3.5 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
		<span class="text-text-primary font-medium">{{ $user->name }}</span>
	</div>

	<x-admin.layout.form-panel>
		<form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
			@csrf
			@method('PUT')
			<x-admin.form.field label="Họ tên" for="name" required :error="$errors->first('name')">
				<x-admin.form.input id="name" name="name" :value="old('name', $user->name)" placeholder="Nguyễn Văn A" required />
			</x-admin.form.field>

			<x-admin.form.field label="Email" for="email" required :error="$errors->first('email')">
				<x-admin.form.input id="email" name="email" type="email" :value="old('email', $user->email)" placeholder="admin@ieltstl.vn" required />
			</x-admin.form.field>

			<x-admin.form.field label="Mật khẩu mới" for="password" hint="(không bắt buộc)" :error="$errors->first('password')">
				<x-admin.form.input id="password" name="password" type="password" placeholder="Để trống nếu không đổi" />
			</x-admin.form.field>

			<x-admin.form.field label="Xác nhận mật khẩu mới" for="password_confirmation" :error="$errors->first('password_confirmation')">
				<x-admin.form.input id="password_confirmation" name="password_confirmation" type="password" placeholder="Nhập lại mật khẩu mới" />
			</x-admin.form.field>
			<div class="flex items-center gap-3 pt-2 border-t border-border-light">
				<button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
					Cập nhật
				</button>
				<a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 border border-border-light rounded-lg text-sm text-text-secondary hover:bg-gray-50 transition-colors cursor-pointer">Hủy</a>
			</div>
		</form>
	</x-admin.layout.form-panel>
</x-admin.layout.app>
