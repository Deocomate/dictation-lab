<x-client.layout.auth title="Đăng nhập — Dictation Lab">
  <x-slot:headerRight>
    <p class="text-sm text-text-secondary">Chưa có tài khoản? <a href="{{ route('register') }}" class="text-brand font-semibold hover:text-brand-dark transition-colors">Đăng ký miễn phí</a></p>
  </x-slot:headerRight>

  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-float border border-border-light p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-text-primary">Chào mừng trở lại!</h1>
        <p class="mt-2 text-sm text-text-secondary">Đăng nhập để tiếp tục luyện chép chính tả song ngữ.</p>
      </div>

      @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-semantic-red">
          {{ $errors->first() }}
        </div>
      @endif

      @if(session('success'))
        <div class="mb-4 p-3 bg-brand-light border border-brand/20 rounded-lg text-sm text-brand">
          {{ session('success') }}
        </div>
      @endif

      <div class="space-y-2 mb-6">
        <a href="{{ route('social.redirect', 'google') }}" class="btn-social flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border border-border-light rounded-lg text-sm font-medium text-text-primary">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.6 12.227c0-.764-.068-1.5-.195-2.21H12v4.183h5.383a4.604 4.604 0 01-2.002 3.022v2.508h3.24c1.895-1.745 2.979-4.318 2.979-7.503z" fill="#4285F4"/><path d="M12 22c2.7 0 4.964-.895 6.619-2.424l-3.24-2.508c-.895.6-2.04.955-3.379.955-2.6 0-4.802-1.755-5.589-4.113H3.062v2.587A9.997 9.997 0 0012 22z" fill="#34A853"/><path d="M6.411 13.91A5.996 5.996 0 016.097 12c0-.664.114-1.309.314-1.909V7.504H3.062A9.996 9.996 0 002 12c0 1.613.386 3.141 1.062 4.496l3.349-2.586z" fill="#FBBC05"/><path d="M12 5.978c1.468 0 2.788.505 3.825 1.5l2.868-2.868C16.959 2.984 14.695 2 12 2A9.997 9.997 0 003.062 7.504l3.349 2.587C7.198 7.733 9.4 5.978 12 5.978z" fill="#EA4335"/></svg>
          Đăng nhập bằng Google
        </a>
        <a href="{{ route('social.redirect', 'facebook') }}" class="btn-social flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border border-border-light rounded-lg text-sm font-medium text-text-primary">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.019 4.388 11.008 10.125 11.927v-8.438H7.078v-3.49h3.047V9.413c0-3.007 1.79-4.669 4.533-4.669 1.313 0 2.686.236 2.686.236v2.953H15.83c-1.492 0-1.956.931-1.956 1.887v2.252h3.328l-.532 3.49h-2.796V24C19.612 23.081 24 18.092 24 12.073z"/></svg>
          Đăng nhập bằng Facebook
        </a>
      </div>

      <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-border-light"></div></div>
        <div class="relative flex justify-center text-xs"><span class="px-3 bg-white text-text-disabled">đăng nhập bằng email</span></div>
      </div>

      <form method="POST" action="{{ route('login.submit') }}" class="space-y-4" novalidate>
        @csrf
        <div>
          <label for="email" class="block text-sm font-medium text-text-primary mb-1.5">Email</label>
          <input id="email" name="email" type="email" autocomplete="email" placeholder="you@example.com" value="{{ old('email') }}"
            class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled transition-all" />
        </div>
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label for="password" class="text-sm font-medium text-text-primary">Mật khẩu</label>
            <a href="{{ route('forgot-password') }}" class="text-xs text-brand hover:text-brand-dark transition-colors font-medium">Quên mật khẩu?</a>
          </div>
          <div class="relative">
            <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Tối thiểu 8 ký tự"
              class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled pr-11 transition-all" />
            <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-text-disabled hover:text-text-secondary transition-colors cursor-pointer" aria-label="Hiện/ẩn mật khẩu">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <input id="remember" name="remember" type="checkbox" class="w-4 h-4 rounded border-border-light cursor-pointer accent-brand" {{ old('remember') ? 'checked' : '' }} />
          <label for="remember" class="text-sm text-text-secondary cursor-pointer">Ghi nhớ đăng nhập</label>
        </div>
        <button type="submit" class="w-full py-3 px-4 bg-brand text-white text-sm font-semibold rounded-lg hover:bg-brand-dark transition-colors duration-200 cursor-pointer mt-2">
          Đăng nhập
        </button>
      </form>
    </div>
    <p class="text-center text-sm text-text-secondary mt-4">
      Bạn là quản trị viên?
      <a href="{{ route('admin.auth.login') }}" class="text-brand font-semibold hover:text-brand-dark transition-colors">Đăng nhập Admin</a>
    </p>
    <p class="text-center text-xs text-text-disabled mt-6">
      Bằng cách đăng nhập, bạn đồng ý với <a href="/terms" class="underline hover:text-text-secondary transition-colors">Điều khoản dịch vụ</a> và <a href="/privacy" class="underline hover:text-text-secondary transition-colors">Chính sách bảo mật</a>.
    </p>
  </div>
</x-client.layout.auth>
