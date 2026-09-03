<x-admin.layout.auth title="Đăng nhập Admin">
	<form method="POST" action="{{ route('admin.auth.login.submit') }}" class="space-y-5">
		@csrf
		<div>
			<label for="email" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Email</label>
			<div class="relative">
				<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
				<input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@ieltstl.vn" class="input-auth w-full border border-border-light rounded-lg pl-9 pr-3.5 py-2.5 text-sm transition-all" required autofocus />
			</div>
		</div>
		<div>
			<label for="password" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Mật khẩu</label>
			<div class="relative">
				<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
				<input id="password" name="password" type="password" placeholder="••••••••" class="input-auth w-full border border-border-light rounded-lg pl-9 pr-3.5 py-2.5 text-sm transition-all" required />
			</div>
		</div>
		<div class="flex items-center justify-between text-sm">
			<label class="flex items-center gap-2 cursor-pointer text-text-secondary">
				<input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="rounded border-border-light text-brand focus:ring-brand" />
				Ghi nhớ đăng nhập
			</label>
			<a href="{{ route('admin.auth.forgot-password') }}" class="text-brand hover:text-brand-dark transition-colors">Quên mật khẩu?</a>
		</div>
		<button type="submit" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
			Đăng nhập
		</button>
	</form>
	<div class="mt-4 text-center space-y-2 text-sm text-text-secondary">
		<p><a href="{{ route('home') }}" class="text-brand hover:text-brand-dark transition-colors">← Về trang chủ</a></p>
		<p>Học viên? <a href="{{ route('login') }}" class="text-brand font-semibold hover:text-brand-dark transition-colors">Đăng nhập học viên</a></p>
	</div>
</x-admin.layout.auth>
