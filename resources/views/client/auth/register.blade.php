<x-client.layout.auth title="Đăng ký — Dictation Lab">
  <x-slot:headerRight>
    <p class="text-sm text-text-secondary">Đã có tài khoản? <a href="{{ route('login') }}" class="text-brand font-semibold hover:text-brand-dark transition-colors">Đăng nhập</a></p>
  </x-slot:headerRight>

  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-float border border-border-light p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-text-primary">Tạo tài khoản miễn phí</h1>
        <p class="mt-2 text-sm text-text-secondary">Bắt đầu chép chính tả bài mẫu đầu tiên trong vòng 30 giây.</p>
      </div>

      @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-semantic-red">
          {{ $errors->first() }}
        </div>
      @endif

      <div class="space-y-2 mb-6">
        <a href="{{ route('social.redirect', 'google') }}" class="btn-social flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border border-border-light rounded-lg text-sm font-medium text-text-primary">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.6 12.227c0-.764-.068-1.5-.195-2.21H12v4.183h5.383a4.604 4.604 0 01-2.002 3.022v2.508h3.24c1.895-1.745 2.979-4.318 2.979-7.503z" fill="#4285F4"/><path d="M12 22c2.7 0 4.964-.895 6.619-2.424l-3.24-2.508c-.895.6-2.04.955-3.379.955-2.6 0-4.802-1.755-5.589-4.113H3.062v2.587A9.997 9.997 0 0012 22z" fill="#34A853"/><path d="M6.411 13.91A5.996 5.996 0 016.097 12c0-.664.114-1.309.314-1.909V7.504H3.062A9.996 9.996 0 002 12c0 1.613.386 3.141 1.062 4.496l3.349-2.586z" fill="#FBBC05"/><path d="M12 5.978c1.468 0 2.788.505 3.825 1.5l2.868-2.868C16.959 2.984 14.695 2 12 2A9.997 9.997 0 003.062 7.504l3.349 2.587C7.198 7.733 9.4 5.978 12 5.978z" fill="#EA4335"/></svg>
          Tiếp tục với Google
        </a>
        <a href="{{ route('social.redirect', 'facebook') }}" class="btn-social flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border border-border-light rounded-lg text-sm font-medium text-text-primary">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.019 4.388 11.008 10.125 11.927v-8.438H7.078v-3.49h3.047V9.413c0-3.007 1.79-4.669 4.533-4.669 1.313 0 2.686.236 2.686.236v2.953H15.83c-1.492 0-1.956.931-1.956 1.887v2.252h3.328l-.532 3.49h-2.796V24C19.612 23.081 24 18.092 24 12.073z"/></svg>
          Tiếp tục với Facebook
        </a>
      </div>

      <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-border-light"></div></div>
        <div class="relative flex justify-center text-xs"><span class="px-3 bg-white text-text-disabled">đăng ký bằng email</span></div>
      </div>

      <form method="POST" action="{{ route('register.submit') }}" class="space-y-4" novalidate>
        @csrf
        <div>
          <label for="name" class="block text-sm font-medium text-text-primary mb-1.5">Họ và tên</label>
          <input id="name" name="name" type="text" placeholder="Nguyễn Văn A" value="{{ old('name') }}"
            class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled transition-all" />
          @error('name') <p class="text-xs text-semantic-red mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-text-primary mb-1.5">Email</label>
          <input id="email" name="email" type="email" autocomplete="email" placeholder="you@example.com" value="{{ old('email') }}"
            class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled transition-all" />
          @error('email') <p class="text-xs text-semantic-red mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-text-primary mb-1.5">Mật khẩu</label>
          <div class="relative">
            <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Tối thiểu 8 ký tự"
              class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled pr-11 transition-all" />
            <button type="button" id="toggle-pw" class="absolute right-3 top-1/2 -translate-y-1/2 text-text-disabled hover:text-text-secondary cursor-pointer" aria-label="Hiện/ẩn mật khẩu">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
          <div class="mt-2 flex gap-1">
            <div id="s1" class="h-1 flex-1 bg-border-light rounded-full transition-colors"></div>
            <div id="s2" class="h-1 flex-1 bg-border-light rounded-full transition-colors"></div>
            <div id="s3" class="h-1 flex-1 bg-border-light rounded-full transition-colors"></div>
            <div id="s4" class="h-1 flex-1 bg-border-light rounded-full transition-colors"></div>
          </div>
          <p id="pw-hint" class="text-xs text-text-disabled mt-1">Mật khẩu cần tối thiểu 8 ký tự</p>
          @error('password') <p class="text-xs text-semantic-red mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-text-primary mb-1.5">Xác nhận mật khẩu</label>
          <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Nhập lại mật khẩu"
            class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled transition-all" />
        </div>
        <button type="submit" class="w-full py-3 px-4 bg-brand text-white text-sm font-semibold rounded-lg hover:bg-brand-dark transition-colors cursor-pointer">
          Tạo tài khoản miễn phí
        </button>
      </form>

      <div class="mt-6 pt-6 border-t border-border-light">
        <p class="text-xs font-medium text-text-secondary mb-3">Tài khoản Free bao gồm:</p>
        <ul class="space-y-2">
          <li class="flex items-center gap-2 text-xs text-text-secondary"><svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 10 bài mẫu chép chính tả miễn phí</li>
          <li class="flex items-center gap-2 text-xs text-text-secondary"><svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Xem phân tích cơ bản bài mẫu Band 6.5 - 7.0</li>
          <li class="flex items-center gap-2 text-xs text-text-secondary"><svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Theo dõi tiến độ học tập</li>
        </ul>
      </div>
    </div>
    <p class="text-center text-xs text-text-disabled mt-6">
      Bằng cách đăng ký, bạn đồng ý với <a href="/terms" class="underline hover:text-text-secondary transition-colors">Điều khoản dịch vụ</a> và <a href="/privacy" class="underline hover:text-text-secondary transition-colors">Chính sách bảo mật</a>.
    </p>
  </div>

  <script>
    document.getElementById('toggle-pw').addEventListener('click', function() {
      const pw = document.getElementById('password');
      pw.type = pw.type === 'password' ? 'text' : 'password';
    });
    document.getElementById('password').addEventListener('input', function() {
      const val = this.value;
      const strength = [val.length >= 8, /[A-Z]/.test(val), /[0-9]/.test(val), /[^A-Za-z0-9]/.test(val)];
      const score = strength.filter(Boolean).length;
      const colors = ['', '#FF5E5E', '#FFD500', '#11A683', '#11A683'];
      const hints = ['', 'Quá yếu', 'Trung bình', 'Khá mạnh', 'Mạnh'];
      [1,2,3,4].forEach(i => {
        document.getElementById('s' + i).style.backgroundColor = i <= score ? colors[score] : '#E1E4E8';
      });
      document.getElementById('pw-hint').textContent = val.length === 0 ? 'Mật khẩu cần tối thiểu 8 ký tự' : hints[score];
      document.getElementById('pw-hint').style.color = val.length === 0 ? '#B9BDC5' : colors[score];
    });
  </script>
</x-client.layout.auth>
