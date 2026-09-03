<x-client.layout.auth title="Đặt lại mật khẩu — Dictation Lab">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-float border border-border-light p-8">
      <div class="w-14 h-14 bg-brand-light rounded-2xl flex items-center justify-center mx-auto mb-6">
        <svg class="w-7 h-7 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      </div>
      <h1 class="text-2xl font-bold text-text-primary text-center">Đặt mật khẩu mới</h1>
      <p class="mt-2 text-sm text-text-secondary text-center">Mật khẩu mới phải khác mật khẩu cũ.</p>

      @if($errors->any())
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-semantic-red">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('reset-password.submit') }}" class="mt-6 space-y-4" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
          <label for="password" class="block text-sm font-medium text-text-primary mb-1.5">Mật khẩu mới</label>
          <div class="relative">
            <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Tối thiểu 8 ký tự"
              class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled pr-11 transition-all" />
            <button type="button" id="toggle-new" class="absolute right-3 top-1/2 -translate-y-1/2 text-text-disabled hover:text-text-secondary cursor-pointer" aria-label="Hiện/ẩn mật khẩu">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
          <div class="mt-2 flex gap-1">
            <div id="s1" class="h-1 flex-1 bg-border-light rounded-full transition-colors"></div>
            <div id="s2" class="h-1 flex-1 bg-border-light rounded-full transition-colors"></div>
            <div id="s3" class="h-1 flex-1 bg-border-light rounded-full transition-colors"></div>
            <div id="s4" class="h-1 flex-1 bg-border-light rounded-full transition-colors"></div>
          </div>
        </div>
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-text-primary mb-1.5">Xác nhận mật khẩu</label>
          <div class="relative">
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Nhập lại mật khẩu"
              class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled pr-11 transition-all" />
            <button type="button" id="toggle-confirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-text-disabled hover:text-text-secondary cursor-pointer" aria-label="Hiện/ẩn mật khẩu">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
        </div>
        <button type="submit" class="w-full py-3 bg-brand text-white text-sm font-semibold rounded-lg hover:bg-brand-dark transition-colors cursor-pointer">
          Cập nhật mật khẩu
        </button>
      </form>
    </div>
  </div>

  <script>
    document.getElementById('toggle-new').addEventListener('click', function() { const el = document.getElementById('password'); el.type = el.type === 'password' ? 'text' : 'password'; });
    document.getElementById('toggle-confirm').addEventListener('click', function() { const el = document.getElementById('password_confirmation'); el.type = el.type === 'password' ? 'text' : 'password'; });
    document.getElementById('password').addEventListener('input', function() {
      const val = this.value;
      const s = [val.length >= 8, /[A-Z]/.test(val), /[0-9]/.test(val), /[^A-Za-z0-9]/.test(val)];
      const score = s.filter(Boolean).length;
      const colors = ['', '#FF5E5E', '#FFD500', '#11A683', '#11A683'];
      [1,2,3,4].forEach(i => { document.getElementById('s' + i).style.backgroundColor = i <= score ? colors[score] : '#E1E4E8'; });
    });
  </script>
</x-client.layout.auth>
