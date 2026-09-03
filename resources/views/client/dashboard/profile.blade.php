<x-client.layout.dashboard title="Hồ sơ cá nhân" activePage="profile">
  <x-slot:headerContent>
    <div>
      <h1 class="text-lg font-bold text-text-primary">Hồ sơ cá nhân</h1>
      <p class="text-xs text-text-secondary">Quản lý thông tin cá nhân và mật khẩu.</p>
    </div>
  </x-slot:headerContent>

  @php $user = auth()->user(); @endphp

  <div class="w-full space-y-6">
    {{-- Avatar + Basic info --}}
    <div class="bg-white rounded-xl border border-border-light p-6">
      <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 rounded-full bg-brand-light flex items-center justify-center text-brand font-bold text-xl">
          {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
          <p class="font-semibold text-text-primary">{{ $user->name }}</p>
          <p class="text-sm text-text-secondary">{{ $user->email }}</p>
          <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full {{ $user->isPro() ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-text-secondary' }} font-medium">
            {{ $user->isPro() ? 'Pro Plan' : 'Free Plan' }}
          </span>
        </div>
      </div>

      <form method="POST" action="{{ route('client.profile.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
          <label for="name" class="block text-sm font-medium text-text-primary mb-1.5">Họ và tên</label>
          <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
            class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary transition-all" />
          @error('name') <p class="text-xs text-semantic-red mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-text-primary mb-1.5">Email</label>
          <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
            class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary transition-all" />
          @error('email') <p class="text-xs text-semantic-red mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex justify-end">
          <button type="submit" class="px-5 py-2.5 bg-brand text-white text-sm font-semibold rounded-lg hover:bg-brand-dark transition-colors cursor-pointer">
            Lưu thay đổi
          </button>
        </div>
      </form>
    </div>

    {{-- Change password --}}
    <div class="bg-white rounded-xl border border-border-light p-6">
      <h2 class="font-semibold text-text-primary mb-4">Đổi mật khẩu</h2>
      <form method="POST" action="{{ route('client.password.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
          <label for="current_password" class="block text-sm font-medium text-text-primary mb-1.5">Mật khẩu hiện tại</label>
          <input id="current_password" name="current_password" type="password"
            class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary transition-all" />
          @error('current_password') <p class="text-xs text-semantic-red mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-text-primary mb-1.5">Mật khẩu mới</label>
          <input id="password" name="password" type="password" placeholder="Tối thiểu 8 ký tự"
            class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled transition-all" />
          @error('password') <p class="text-xs text-semantic-red mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-text-primary mb-1.5">Xác nhận mật khẩu mới</label>
          <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Nhập lại mật khẩu mới"
            class="w-full px-4 py-3 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled transition-all" />
        </div>
        <div class="flex justify-end">
          <button type="submit" class="px-5 py-2.5 bg-brand text-white text-sm font-semibold rounded-lg hover:bg-brand-dark transition-colors cursor-pointer">
            Cập nhật mật khẩu
          </button>
        </div>
      </form>
    </div>
  </div>
</x-client.layout.dashboard>
