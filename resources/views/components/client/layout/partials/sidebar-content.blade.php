@php $user = auth()->user(); @endphp

<div class="px-5 py-5 border-b border-border-light flex items-center justify-between">
  <x-client.layout.brand-logo size="md" />
  <button onclick="closeMobileMenu()" class="text-text-disabled hover:text-text-secondary cursor-pointer lg:hidden">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
  </button>
</div>

<nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
  <a href="{{ route('client.dashboard') }}" class="sidebar-link {{ $activePage === 'dashboard' ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-text-secondary cursor-pointer">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tổng quan
  </a>
  <a href="{{ route('client.articles.library') }}" class="sidebar-link {{ in_array($activePage, ['articles', 'library'], true) ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-text-secondary cursor-pointer">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    Thư viện bài viết
  </a>
  <a href="{{ route('client.vocabulary') }}" class="sidebar-link {{ $activePage === 'vocabulary' ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-text-secondary cursor-pointer">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
    Sổ tay từ vựng
  </a>
  <div class="pt-2 border-t border-border-light mt-2">
    <a href="{{ route('client.billing') }}" class="sidebar-link {{ $activePage === 'billing' ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-text-secondary cursor-pointer">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
      Gói cước
    </a>
    <a href="{{ route('client.profile') }}" class="sidebar-link {{ $activePage === 'profile' ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-text-secondary cursor-pointer">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      Hồ sơ cá nhân
    </a>
    @if ($user->isAdmin())
      <a href="{{ route('admin.dashboard') }}" target="_blank" rel="noopener noreferrer" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-text-secondary cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Admin Panel
      </a>
    @endif
  </div>
</nav>

<div class="px-4 py-4 border-t border-border-light">
  <div class="flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-brand-light flex items-center justify-center text-brand font-semibold text-sm">
      {{ mb_substr($user->name ?? 'U', 0, 1) }}
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-sm font-medium text-text-primary truncate">{{ $user->name }}</p>
      <p class="text-xs text-text-disabled truncate">{{ $user->isPro() ? 'Pro Plan' : 'Free Plan' }}</p>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="text-text-disabled hover:text-text-secondary cursor-pointer" title="Đăng xuất">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      </button>
    </form>
  </div>
</div>
