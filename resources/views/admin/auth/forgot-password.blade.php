<x-admin.layout.auth title="Quên mật khẩu Admin">
	<p class="text-sm text-text-secondary mb-5">Nhập email quản trị để nhận link đặt lại mật khẩu.</p>
	<form method="POST" action="{{ route('admin.auth.forgot-password.submit') }}" class="space-y-5">
		@csrf
		<div>
			<label for="email" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Email quản trị</label>
			<div class="relative">
				<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
				<input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@ieltstl.vn" class="input-auth w-full border border-border-light rounded-lg pl-9 pr-3.5 py-2.5 text-sm transition-all" required autofocus />
			</div>
		</div>
		<button type="submit" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">Gửi link đặt lại</button>
	</form>

	<a href="{{ route('admin.auth.login') }}" class="mt-5 flex items-center justify-center gap-1 text-sm text-text-secondary hover:text-brand transition-colors">
		<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
		Quay lại đăng nhập
	</a>
</x-admin.layout.auth>
