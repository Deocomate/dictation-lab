  <!-- ============================================================
       SECTION 1: NAVIGATION BAR (Fixed, Floating, Blur)
       ============================================================ -->
  @php
    $navLinkBase = 'text-sm font-medium transition-colors duration-200 cursor-pointer';
    $activeNav = 'text-text-primary';
    $inactiveNav = 'text-text-secondary hover:text-text-primary';
  @endphp

  <nav class="navbar-blur fixed top-0 left-0 right-0 z-50 border-b border-border-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <x-client.layout.brand-logo size="md" />

        <!-- Desktop Nav Links -->
        <div class="hidden md:flex items-center gap-8">
          <a href="{{ route('home') }}" class="{{ $navLinkBase }} {{ request()->routeIs('home') ? $activeNav : $inactiveNav }}">Trang chủ</a>
          <a href="{{ route('client.home.about') }}" class="{{ $navLinkBase }} {{ request()->routeIs('client.home.about') ? $activeNav : $inactiveNav }}">Giới thiệu</a>
          <a href="{{ route('client.home.features') }}" class="{{ $navLinkBase }} {{ request()->routeIs('client.home.features') ? $activeNav : $inactiveNav }}">Chức năng</a>
          <a href="{{ route('client.home.pricing-contact') }}" class="{{ $navLinkBase }} {{ request()->routeIs('client.home.pricing-contact') ? $activeNav : $inactiveNav }}">Gói & Liên hệ</a>
        </div>

        <!-- CTA Buttons -->
        <div class="flex items-center gap-3">
          @include('components.client.layout.partials.nav-auth-actions', ['variant' => 'desktop'])
          <!-- Mobile Menu Button -->
          <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-text-secondary hover:text-text-primary hover:bg-gray-100 transition-colors duration-200 cursor-pointer focus-visible:ring-2 focus-visible:ring-brand" aria-label="Mở menu" aria-expanded="false">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div id="mobile-menu" class="md:hidden hidden border-t border-border-light bg-white">
      <div class="px-4 py-4 space-y-3">
        <a href="{{ route('home') }}" class="block text-sm font-medium text-text-secondary hover:text-text-primary transition-colors cursor-pointer">Trang chủ</a>
        <a href="{{ route('client.home.about') }}" class="block text-sm font-medium text-text-secondary hover:text-text-primary transition-colors cursor-pointer">Giới thiệu</a>
        <a href="{{ route('client.home.features') }}" class="block text-sm font-medium text-text-secondary hover:text-text-primary transition-colors cursor-pointer">Chức năng</a>
        <a href="{{ route('client.home.pricing-contact') }}" class="block text-sm font-medium text-text-secondary hover:text-text-primary transition-colors cursor-pointer">Gói & Liên hệ</a>
        <hr class="border-border-light" />
        @include('components.client.layout.partials.nav-auth-actions', ['variant' => 'mobile'])
      </div>
    </div>
  </nav>
