<x-client.layout.auth title="Quên mật khẩu — Dictation Lab">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-float border border-border-light p-8">
      <div class="w-14 h-14 bg-brand-light rounded-2xl flex items-center justify-center mx-auto mb-6">
        <svg class="w-7 h-7 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      </div>
      <h1 class="text-2xl font-bold text-text-primary text-center">Quên mật khẩu?</h1>
      <p class="mt-2 text-sm text-text-secondary text-center leading-relaxed">Nhập email của bạn. Chúng tôi sẽ gửi link đặt lại mật khẩu.</p>

      @if(session('success'))
        <div class="mt-4 p-3 bg-brand-light border border-brand/20 rounded-lg text-sm text-brand">
          {{ session('success') }}
        </div>
      @endif
      @if($errors->any())
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-semantic-red">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('forgot-password.submit') }}" class="mt-6 space-y-4" novalidate>
        @csrf
        <div>
          <label for="email" class="block text-sm font-medium text-text-primary mb-1.5">Email</label>
          <input id="email" name="email" type="email" autocomplete="email" placeholder="you@example.com" value="{{ old('email') }}"
            class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled transition-all" />
        </div>
        <button type="submit" class="w-full py-3 bg-brand text-white text-sm font-semibold rounded-lg hover:bg-brand-dark transition-colors cursor-pointer">
          Gửi link đặt lại mật khẩu
        </button>
      </form>
      <div class="mt-5 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-sm text-text-secondary hover:text-text-primary transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          Quay lại đăng nhập
        </a>
      </div>
    </div>
  </div>
</x-client.layout.auth>
