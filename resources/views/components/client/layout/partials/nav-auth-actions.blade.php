@props(['variant' => 'desktop'])

@php
  $isMobile = $variant === 'mobile';
  $textLinkClass = $isMobile
    ? 'block text-sm font-medium text-text-secondary hover:text-text-primary transition-colors cursor-pointer'
    : 'text-sm font-medium text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer hidden sm:inline-block';
  $registerClass = $isMobile
    ? 'block w-full text-center px-4 py-2 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors cursor-pointer'
    : 'inline-flex items-center px-4 py-2 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors duration-200 cursor-pointer focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2';
  $logoutButtonClass = $isMobile
    ? 'w-full text-center px-4 py-2 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors cursor-pointer'
    : 'inline-flex items-center px-4 py-2 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors duration-200 cursor-pointer focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2';
  $user = auth()->user();
@endphp

@guest
  <a href="{{ route('login') }}" class="{{ $textLinkClass }}">
    Đăng nhập học viên
  </a>
  <a href="{{ route('admin.auth.login') }}" class="{{ $textLinkClass }}">
    Đăng nhập Admin
  </a>
  <a href="{{ route('register') }}" class="{{ $registerClass }}">
    Bắt đầu miễn phí
  </a>
@else
  @if ($user->isAdmin())
    <a href="{{ route('client.dashboard') }}" class="{{ $textLinkClass }}">
      Khu học viên
    </a>
    <a href="{{ route('admin.dashboard') }}" class="{{ $textLinkClass }}">
      Admin Panel
    </a>
  @else
    <a href="{{ route('client.dashboard') }}" class="{{ $textLinkClass }}">
      Dashboard
    </a>
  @endif
  <form method="POST" action="{{ route('logout') }}" class="{{ $isMobile ? 'block w-full' : 'inline' }}">
    @csrf
    <button type="submit" class="{{ $logoutButtonClass }}">
      Đăng xuất
    </button>
  </form>
@endguest
