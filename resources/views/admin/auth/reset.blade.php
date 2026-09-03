<x-admin.layout.auth title="Đặt lại mật khẩu Admin">
	<form method="POST" action="{{ route('admin.auth.reset-password.submit') }}" class="space-y-5">
		@csrf
		<input type="hidden" name="token" value="{{ $token }}" />
		<div>
			<label for="email" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Email</label>
			<input id="email" name="email" type="email" value="{{ old('email', $email ?? '') }}" class="input-auth w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
		</div>
		<div>
			<label for="password" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Mật khẩu mới</label>
			<input id="password" name="password" type="password" placeholder="Tối thiểu 8 ký tự" class="input-auth w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
		</div>
		<div>
			<label for="password_confirmation" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Xác nhận mật khẩu</label>
			<input id="password_confirmation" name="password_confirmation" type="password" placeholder="Nhập lại mật khẩu mới" class="input-auth w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
		</div>
		<button type="submit" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">Cập nhật mật khẩu</button>
	</form>
</x-admin.layout.auth>
